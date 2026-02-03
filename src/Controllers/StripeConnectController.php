<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\StripeConnectService;

class StripeConnectController extends Controller
{
    private $stripeConnect;

    public function __construct()
    {
        parent::__construct();
        $this->stripeConnect = new StripeConnectService($this->db, $this->config);
    }

    /**
     * Show Stripe Connect setup page
     */
    public function setup()
    {
        $this->requireAuth();
        $business = $this->getBusinessForUser();

        if (!$business) {
            header('Location: /business/register');
            exit;
        }

        // Sync current status if account exists
        if ($business['stripe_account_id']) {
            $this->stripeConnect->syncAccountStatus($business['id']);
            // Refresh business data
            $stmt = $this->db->prepare("SELECT * FROM businesses WHERE id = ?");
            $stmt->execute([$business['id']]);
            $business = $stmt->fetch(\PDO::FETCH_ASSOC);
        }

        $this->render('pages/business/stripe-connect', [
            'business' => $business,
            'isConnected' => $this->stripeConnect->isConnected($business),
            'pageTitle' => 'Stripe Connect'
        ]);
    }

    /**
     * Start Stripe Connect onboarding
     */
    public function connect()
    {
        $this->requireAuth();
        $business = $this->getBusinessForUser();

        if (!$business) {
            $_SESSION['error'] = 'Geen salon gevonden';
            header('Location: /business/dashboard');
            exit;
        }

        // Create account if not exists
        if (!$business['stripe_account_id']) {
            $result = $this->stripeConnect->createConnectedAccount($business);
            if (!$result) {
                $_SESSION['error'] = 'Kon Stripe account niet aanmaken';
                header('Location: /business/stripe-connect');
                exit;
            }
            $accountId = $result['account_id'];
        } else {
            $accountId = $business['stripe_account_id'];
        }

        // Generate onboarding link
        $onboardingUrl = $this->stripeConnect->createAccountLink($accountId, $business['slug']);

        if (!$onboardingUrl) {
            $_SESSION['error'] = 'Kon onboarding link niet genereren';
            header('Location: /business/stripe-connect');
            exit;
        }

        header('Location: ' . $onboardingUrl);
        exit;
    }

    /**
     * Return URL after Stripe onboarding
     */
    public function returnUrl()
    {
        $this->requireAuth();
        $business = $this->getBusinessForUser();

        if (!$business || !$business['stripe_account_id']) {
            header('Location: /business/stripe-connect');
            exit;
        }

        // Sync account status
        $status = $this->stripeConnect->syncAccountStatus($business['id']);

        if ($status && $status['status'] === 'completed') {
            $_SESSION['success'] = 'Stripe Connect is succesvol gekoppeld! Betalingen worden nu automatisch gesplitst.';
        } elseif ($status && $status['status'] === 'in_progress') {
            $_SESSION['info'] = 'Je Stripe account wordt nog geverifieerd. Je ontvangt een e-mail wanneer dit klaar is.';
        } else {
            $_SESSION['warning'] = 'Onboarding is nog niet voltooid. Klik op "Verder gaan" om door te gaan.';
        }

        header('Location: /business/stripe-connect');
        exit;
    }

    /**
     * Refresh URL when onboarding link expires
     */
    public function refresh()
    {
        $this->requireAuth();
        $business = $this->getBusinessForUser();

        if (!$business || !$business['stripe_account_id']) {
            header('Location: /business/stripe-connect');
            exit;
        }

        // Generate new onboarding link
        $onboardingUrl = $this->stripeConnect->createAccountLink($business['stripe_account_id'], $business['slug']);

        if (!$onboardingUrl) {
            $_SESSION['error'] = 'Kon nieuwe onboarding link niet genereren';
            header('Location: /business/stripe-connect');
            exit;
        }

        header('Location: ' . $onboardingUrl);
        exit;
    }

    /**
     * Open Stripe Express Dashboard
     */
    public function dashboard()
    {
        $this->requireAuth();
        $business = $this->getBusinessForUser();

        if (!$business || !$business['stripe_account_id']) {
            $_SESSION['error'] = 'Geen Stripe account gekoppeld';
            header('Location: /business/stripe-connect');
            exit;
        }

        $dashboardUrl = $this->stripeConnect->getLoginLink($business['stripe_account_id']);

        if (!$dashboardUrl) {
            $_SESSION['error'] = 'Kon Stripe dashboard niet openen';
            header('Location: /business/stripe-connect');
            exit;
        }

        header('Location: ' . $dashboardUrl);
        exit;
    }

    /**
     * Disconnect Stripe account
     */
    public function disconnect()
    {
        $this->requireAuth();
        $this->requireCsrf();
        $business = $this->getBusinessForUser();

        if (!$business) {
            $_SESSION['error'] = 'Geen salon gevonden';
            header('Location: /business/dashboard');
            exit;
        }

        if ($this->stripeConnect->disconnect($business['id'])) {
            $_SESSION['success'] = 'Stripe Connect is losgekoppeld';
        } else {
            $_SESSION['error'] = 'Kon Stripe Connect niet loskoppelen';
        }

        header('Location: /business/stripe-connect');
        exit;
    }

    /**
     * API: Get connection status
     */
    public function status()
    {
        $this->requireAuth();
        $business = $this->getBusinessForUser();

        if (!$business) {
            $this->json(['error' => 'No business found'], 404);
            return;
        }

        $status = null;
        if ($business['stripe_account_id']) {
            $status = $this->stripeConnect->syncAccountStatus($business['id']);
        }

        $this->json([
            'connected' => $this->stripeConnect->isConnected($business),
            'account_id' => $business['stripe_account_id'],
            'status' => $status
        ]);
    }

    /**
     * Helper: Get business for logged in user
     */
    private function getBusinessForUser(): ?array
    {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) return null;

        $stmt = $this->db->prepare("SELECT * FROM businesses WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }
}
