<?php
namespace GlamourSchedule\Controllers;

use GlamourSchedule\Core\Controller;
use GlamourSchedule\Core\Mailer;

/**
 * Mollie Connect Controller
 * Handles OAuth onboarding for salons to receive automatic payouts
 */
class MollieConnectController extends Controller
{
    private string $clientId;
    private string $clientSecret;
    private string $redirectUri;
    private string $apiKey;

    // Platform fee per booking
    private const PLATFORM_FEE = 1.75;

    public function __construct()
    {
        parent::__construct();

        $this->clientId = getenv('MOLLIE_CLIENT_ID') ?: '';
        $this->clientSecret = getenv('MOLLIE_CLIENT_SECRET') ?: '';
        $this->redirectUri = 'https://glamourschedule.com/business/mollie/callback';
        $this->apiKey = getenv('MOLLIE_API_KEY') ?: '';
    }

    /**
     * Show Mollie Connect onboarding page
     */
    public function showOnboarding(): string
    {
        $this->requireBusinessAuth();
        $business = $this->getCurrentBusiness();

        return $this->view('pages/business/mollie-connect', [
            'business' => $business,
            'isConnected' => !empty($business['mollie_account_id']),
            'onboardingStatus' => $business['mollie_onboarding_status'] ?? 'pending'
        ]);
    }

    /**
     * Start OAuth authorization flow
     */
    public function startAuthorization(): void
    {
        $this->requireBusinessAuth();
        $business = $this->getCurrentBusiness();

        // Generate state token for CSRF protection
        $state = bin2hex(random_bytes(32));
        $_SESSION['mollie_oauth_state'] = $state;
        $_SESSION['mollie_oauth_business_id'] = $business['id'];

        // Build authorization URL
        $params = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'state' => $state,
            'scope' => 'payments.read payments.write profiles.read organizations.read',
            'response_type' => 'code',
            'approval_prompt' => 'auto'
        ];

        $authUrl = 'https://www.mollie.com/oauth2/authorize?' . http_build_query($params);

        header('Location: ' . $authUrl);
        exit;
    }

    /**
     * Handle OAuth callback from Mollie
     */
    public function handleCallback(): string
    {
        $code = $_GET['code'] ?? null;
        $state = $_GET['state'] ?? null;
        $error = $_GET['error'] ?? null;

        // Check for errors
        if ($error) {
            return $this->redirectWithError('/business/mollie/connect', 'Mollie autorisatie geannuleerd: ' . ($error ?? 'Onbekende fout'));
        }

        // Verify state
        if (!$state || $state !== ($_SESSION['mollie_oauth_state'] ?? '')) {
            return $this->redirectWithError('/business/mollie/connect', 'Ongeldige sessie. Probeer opnieuw.');
        }

        $businessId = $_SESSION['mollie_oauth_business_id'] ?? null;
        if (!$businessId) {
            return $this->redirectWithError('/business/mollie/connect', $this->t('error_session_expired'));
        }

        // Exchange code for tokens
        $tokens = $this->exchangeCodeForTokens($code);

        if (!$tokens) {
            return $this->redirectWithError('/business/mollie/connect', 'Kon geen toegang krijgen tot Mollie. Probeer opnieuw.');
        }

        // Get organization info
        $orgInfo = $this->getOrganizationInfo($tokens['access_token']);

        if (!$orgInfo) {
            return $this->redirectWithError('/business/mollie/connect', 'Kon organisatie gegevens niet ophalen.');
        }

        // Get profile info
        $profileInfo = $this->getProfileInfo($tokens['access_token']);

        // Update business with Mollie Connect data
        $this->db->query(
            "UPDATE businesses SET
                mollie_account_id = ?,
                mollie_profile_id = ?,
                mollie_onboarding_status = 'completed',
                mollie_access_token = ?,
                mollie_refresh_token = ?,
                mollie_connected_at = NOW()
             WHERE id = ?",
            [
                $orgInfo['id'] ?? null,
                $profileInfo['id'] ?? null,
                $tokens['access_token'],
                $tokens['refresh_token'] ?? null,
                $businessId
            ]
        );

        // Clear session data
        unset($_SESSION['mollie_oauth_state']);
        unset($_SESSION['mollie_oauth_business_id']);

        // Get business info for email
        $stmt = $this->db->query("SELECT * FROM businesses WHERE id = ?", [$businessId]);
        $business = $stmt->fetch(\PDO::FETCH_ASSOC);

        // Send confirmation email
        $this->sendConnectionConfirmation($business);

        // Redirect with success
        $_SESSION['flash_success'] = 'Mollie account succesvol gekoppeld! Je ontvangt nu automatisch uitbetalingen.';
        header('Location: /business/mollie/connect');
        exit;
    }

    /**
     * Disconnect Mollie account
     */
    public function disconnect(): string
    {
        $this->requireBusinessAuth();
        $business = $this->getCurrentBusiness();

        // Check if there are pending payouts
        $stmt = $this->db->query(
            "SELECT COUNT(*) as count FROM business_payouts WHERE business_id = ? AND status IN ('pending', 'processing')",
            [$business['id']]
        );
        $pending = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($pending['count'] > 0) {
            $_SESSION['flash_error'] = 'Je hebt nog openstaande uitbetalingen. Koppel pas los na afronding.';
            header('Location: /business/mollie/connect');
            exit;
        }

        // Revoke tokens if possible
        if (!empty($business['mollie_access_token'])) {
            $this->revokeTokens($business['mollie_access_token']);
        }

        // Clear Mollie data
        $this->db->query(
            "UPDATE businesses SET
                mollie_account_id = NULL,
                mollie_profile_id = NULL,
                mollie_onboarding_status = 'pending',
                mollie_access_token = NULL,
                mollie_refresh_token = NULL,
                mollie_connected_at = NULL
             WHERE id = ?",
            [$business['id']]
        );

        $_SESSION['flash_success'] = 'Mollie account losgekoppeld.';
        header('Location: /business/mollie/connect');
        exit;
    }

    /**
     * Exchange authorization code for access tokens
     */
    private function exchangeCodeForTokens(string $code): ?array
    {
        $ch = curl_init('https://api.mollie.com/oauth2/tokens');

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $this->redirectUri
            ]),
            CURLOPT_HTTPHEADER => [
                'Authorization: Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret),
                'Content-Type: application/x-www-form-urlencoded'
            ]
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            $this->logError("Token exchange failed: HTTP $httpCode - $response");
            return null;
        }

        return json_decode($response, true);
    }

    /**
     * Refresh access token
     */
    public function refreshAccessToken(int $businessId): ?string
    {
        $stmt = $this->db->query(
            "SELECT mollie_refresh_token FROM businesses WHERE id = ?",
            [$businessId]
        );
        $business = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (empty($business['mollie_refresh_token'])) {
            return null;
        }

        $ch = curl_init('https://api.mollie.com/oauth2/tokens');

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'grant_type' => 'refresh_token',
                'refresh_token' => $business['mollie_refresh_token']
            ]),
            CURLOPT_HTTPHEADER => [
                'Authorization: Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret),
                'Content-Type: application/x-www-form-urlencoded'
            ]
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            $this->logError("Token refresh failed for business $businessId: HTTP $httpCode");
            return null;
        }

        $tokens = json_decode($response, true);

        // Update tokens in database
        $this->db->query(
            "UPDATE businesses SET mollie_access_token = ?, mollie_refresh_token = ? WHERE id = ?",
            [$tokens['access_token'], $tokens['refresh_token'] ?? $business['mollie_refresh_token'], $businessId]
        );

        return $tokens['access_token'];
    }

    /**
     * Get organization info from Mollie
     */
    private function getOrganizationInfo(string $accessToken): ?array
    {
        $ch = curl_init('https://api.mollie.com/v2/organizations/me');

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $accessToken
            ]
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    /**
     * Get profile info from Mollie
     */
    private function getProfileInfo(string $accessToken): ?array
    {
        $ch = curl_init('https://api.mollie.com/v2/profiles/me');

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $accessToken
            ]
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    /**
     * Revoke tokens
     */
    private function revokeTokens(string $accessToken): void
    {
        $ch = curl_init('https://api.mollie.com/oauth2/tokens');

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'DELETE',
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $accessToken
            ]
        ]);

        curl_exec($ch);
        curl_close($ch);
    }

    /**
     * Send connection confirmation email
     */
    private function sendConnectionConfirmation(array $business): void
    {
        $mailer = new Mailer();

        $subject = 'Mollie Account Gekoppeld - Automatische Uitbetalingen Actief';

        $body = "
        <div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;'>
            <div style='background:#000;padding:25px;text-align:center;'>
                <h1 style='color:#fff;margin:0;font-size:20px;'>Mollie Account Gekoppeld</h1>
            </div>
            <div style='padding:25px;background:#1a1a1a;'>
                <p>Beste {$business['company_name']},</p>

                <p>Geweldig nieuws! Je Mollie account is succesvol gekoppeld aan GlamourSchedule.</p>

                <div style='background:#f0fdf4;border:1px solid #22c55e;border-radius:8px;padding:20px;margin:20px 0;'>
                    <h3 style='margin:0 0 10px;color:#166534;'>Automatische Uitbetalingen Actief</h3>
                    <p style='margin:0;color:#166534;'>
                        Vanaf nu ontvang je automatisch uitbetalingen voor voltooide boekingen.
                    </p>
                </div>

                <h3>Hoe het werkt:</h3>
                <ol style='color:#cccccc;line-height:1.8;'>
                    <li>Klant boekt en betaalt online</li>
                    <li>Klant komt langs en jij scant de QR-code</li>
                    <li>24 uur na de scan wordt het bedrag automatisch uitbetaald</li>
                    <li>Je ontvangt het bedrag minus €" . number_format(self::PLATFORM_FEE, 2, ',', '.') . " platformkosten</li>
                </ol>

                <h3>Rekenvoorbeeld:</h3>
                <table style='width:100%;border-collapse:collapse;margin:15px 0;'>
                    <tr style='background:#0a0a0a;'>
                        <td style='padding:10px;'>Behandeling</td>
                        <td style='padding:10px;text-align:right;'>€50,00</td>
                    </tr>
                    <tr>
                        <td style='padding:10px;'>Platformkosten</td>
                        <td style='padding:10px;text-align:right;color:#dc2626;'>-€" . number_format(self::PLATFORM_FEE, 2, ',', '.') . "</td>
                    </tr>
                    <tr style='background:#f0fdf4;font-weight:bold;'>
                        <td style='padding:10px;'>Jouw uitbetaling</td>
                        <td style='padding:10px;text-align:right;color:#22c55e;'>€" . number_format(50 - self::PLATFORM_FEE, 2, ',', '.') . "</td>
                    </tr>
                </table>

                <p style='color:#cccccc;font-size:13px;'>
                    Je kunt je uitbetalingen bekijken in je dashboard onder 'Uitbetalingen'.
                </p>
            </div>
            <div style='background:#0a0a0a;padding:15px;text-align:center;border-top:1px solid #333;'>
                <p style='margin:0;color:#999;font-size:12px;'>© " . date('Y') . " GlamourSchedule</p>
            </div>
        </div>";

        $mailer->send($business['email'], $subject, $body);
    }

    /**
     * Redirect with error message
     */
    private function redirectWithError(string $url, string $message): string
    {
        $_SESSION['flash_error'] = $message;
        header('Location: ' . $url);
        exit;
    }

    /**
     * Log errors
     */
    private function logError(string $message): void
    {
        $logFile = BASE_PATH . '/storage/logs/mollie-connect.log';
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($logFile, "[$timestamp] ERROR: $message\n", FILE_APPEND);
    }

    /**
     * Get current business from session
     */
    protected function getCurrentBusiness(): ?array
    {
        $businessId = $_SESSION['business_id'] ?? null;
        if (!$businessId) {
            return null;
        }

        $stmt = $this->db->query("SELECT * FROM businesses WHERE id = ?", [$businessId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Require business authentication
     */
    private function requireBusinessAuth(): void
    {
        if (empty($_SESSION['business_id'])) {
            header('Location: /business/login');
            exit;
        }
    }
}
