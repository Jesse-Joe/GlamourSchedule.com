<?php
namespace GlamourSchedule\Services;

use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * Invoice Service
 * Generates PDF invoices for business and sales partner payouts
 */
class InvoiceService
{
    private string $storagePath;
    private Dompdf $dompdf;

    public function __construct()
    {
        $this->storagePath = BASE_PATH . '/storage/invoices';

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'Helvetica');

        $this->dompdf = new Dompdf($options);
    }

    /**
     * Generate invoice for business payout
     * Returns the file path of the generated PDF
     */
    public function generateBusinessInvoice(array $payout): string
    {
        $invoiceNumber = $this->generateInvoiceNumber('BUS', $payout['payout_id']);
        $filename = "factuur-{$invoiceNumber}.pdf";
        $filepath = $this->storagePath . '/' . $filename;

        $html = $this->renderBusinessInvoiceHtml($payout, $invoiceNumber);

        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();

        file_put_contents($filepath, $this->dompdf->output());

        return $filepath;
    }

    /**
     * Generate invoice for sales partner payout
     * Returns the file path of the generated PDF
     */
    public function generateSalesInvoice(array $payout): string
    {
        $invoiceNumber = $this->generateInvoiceNumber('SAL', $payout['payout_id']);
        $filename = "factuur-{$invoiceNumber}.pdf";
        $filepath = $this->storagePath . '/' . $filename;

        $html = $this->renderSalesInvoiceHtml($payout, $invoiceNumber);

        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();

        file_put_contents($filepath, $this->dompdf->output());

        return $filepath;
    }

    /**
     * Generate unique invoice number
     */
    private function generateInvoiceNumber(string $prefix, int $payoutId): string
    {
        return $prefix . '-' . date('Y') . '-' . str_pad($payoutId, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Render business invoice HTML
     */
    private function renderBusinessInvoiceHtml(array $payout, string $invoiceNumber): string
    {
        $companyName = htmlspecialchars($payout['company_name']);
        $email = htmlspecialchars($payout['email']);
        $totalPayout = number_format($payout['total_payout'], 2, ',', '.');
        $totalServiceAmount = number_format($payout['total_service_amount'], 2, ',', '.');
        $totalPlatformFee = number_format($payout['total_platform_fee'], 2, ',', '.');
        $bookingsCount = count($payout['bookings']);
        $df = new \GlamourSchedule\Core\DateFormatter();
        $periodStart = isset($payout['period_start']) ? $df->formatDate($payout['period_start']) : $df->formatDate(strtotime('-7 days'));
        $periodEnd = isset($payout['period_end']) ? $df->formatDate($payout['period_end']) : $df->formatDate(time());
        $invoiceDate = $df->formatDate(time());

        $bookingsHtml = '';
        foreach ($payout['bookings'] as $booking) {
            $serviceName = htmlspecialchars($booking['service_name']);
            $bookingNumber = htmlspecialchars($booking['booking_number']);
            $servicePrice = number_format($booking['service_price'], 2, ',', '.');
            $platformFee = number_format($booking['platform_fee'], 2, ',', '.');
            $payoutAmount = number_format($booking['payout_amount'], 2, ',', '.');

            $bookingsHtml .= <<<HTML
            <tr>
                <td>{$bookingNumber}</td>
                <td>{$serviceName}</td>
                <td class="right">&euro;{$servicePrice}</td>
                <td class="right">&euro;{$platformFee}</td>
                <td class="right">&euro;{$payoutAmount}</td>
            </tr>
HTML;
        }

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            margin: 0;
            padding: 40px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            border-bottom: 2px solid #8B5CF6;
            padding-bottom: 20px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #8B5CF6;
        }
        .invoice-info {
            text-align: right;
        }
        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            color: #8B5CF6;
            margin-bottom: 5px;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #666;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background: #f5f5f5;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border-bottom: 2px solid #ddd;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .right {
            text-align: right;
        }
        .totals {
            margin-top: 20px;
            border-top: 2px solid #8B5CF6;
            padding-top: 15px;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
        }
        .totals-row.final {
            font-size: 16px;
            font-weight: bold;
            color: #8B5CF6;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            margin-top: 10px;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
            text-align: center;
        }
        .address {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <table style="border: none; margin-bottom: 40px;">
        <tr>
            <td style="border: none; padding: 0;">
                <div class="logo">GlamourSchedule</div>
                <div style="color: #666; margin-top: 5px;">Beauty Booking Platform</div>
            </td>
            <td style="border: none; padding: 0; text-align: right;">
                <div class="invoice-title">FACTUUR</div>
                <div><strong>Factuurnummer:</strong> {$invoiceNumber}</div>
                <div><strong>Factuurdatum:</strong> {$invoiceDate}</div>
            </td>
        </tr>
    </table>

    <div class="section">
        <div class="section-title">Factuur voor</div>
        <div class="address">
            <strong>{$companyName}</strong><br>
            {$email}
        </div>
    </div>

    <div class="section">
        <div class="section-title">Uitbetalingsoverzicht - Periode {$periodStart} t/m {$periodEnd}</div>
        <table>
            <thead>
                <tr>
                    <th>Boeking</th>
                    <th>Dienst</th>
                    <th class="right">Bedrag</th>
                    <th class="right">Platformkosten</th>
                    <th class="right">Netto</th>
                </tr>
            </thead>
            <tbody>
                {$bookingsHtml}
            </tbody>
        </table>
    </div>

    <div class="totals">
        <table style="width: 300px; margin-left: auto;">
            <tr>
                <td style="border: none;">Totaal diensten ({$bookingsCount}x)</td>
                <td style="border: none; text-align: right;">&euro;{$totalServiceAmount}</td>
            </tr>
            <tr>
                <td style="border: none;">Platformkosten</td>
                <td style="border: none; text-align: right;">-&euro;{$totalPlatformFee}</td>
            </tr>
            <tr style="border-top: 2px solid #8B5CF6;">
                <td style="border: none; font-weight: bold; font-size: 14px; color: #8B5CF6; padding-top: 10px;">Totaal uitbetaling</td>
                <td style="border: none; font-weight: bold; font-size: 14px; color: #8B5CF6; text-align: right; padding-top: 10px;">&euro;{$totalPayout}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>
            GlamourSchedule B.V. | KvK: 12345678 | BTW: NL123456789B01<br>
            Dit is een automatisch gegenereerde factuur. Voor vragen kunt u contact opnemen via info@glamourschedule.com
        </p>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Render sales partner invoice HTML
     */
    private function renderSalesInvoiceHtml(array $payout, string $invoiceNumber): string
    {
        $salesName = htmlspecialchars($payout['sales_name']);
        $salesEmail = htmlspecialchars($payout['sales_email']);
        $totalCommission = number_format($payout['total_commission'], 2, ',', '.');
        $referralCount = $payout['referral_count'];
        $invoiceDate = date('d-m-Y');

        $referralsHtml = '';
        if (isset($payout['referrals'])) {
            foreach ($payout['referrals'] as $referral) {
                $businessName = htmlspecialchars($referral['company_name']);
                $commission = number_format($referral['commission'], 2, ',', '.');
                $createdAt = date('d-m-Y', strtotime($referral['created_at']));

                $referralsHtml .= <<<HTML
                <tr>
                    <td>{$businessName}</td>
                    <td>{$createdAt}</td>
                    <td class="right">&euro;{$commission}</td>
                </tr>
HTML;
            }
        }

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            margin: 0;
            padding: 40px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            border-bottom: 2px solid #8B5CF6;
            padding-bottom: 20px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #8B5CF6;
        }
        .invoice-info {
            text-align: right;
        }
        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            color: #8B5CF6;
            margin-bottom: 5px;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #666;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background: #f5f5f5;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border-bottom: 2px solid #ddd;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .right {
            text-align: right;
        }
        .totals {
            margin-top: 20px;
            border-top: 2px solid #8B5CF6;
            padding-top: 15px;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
            text-align: center;
        }
        .address {
            margin-bottom: 15px;
        }
        .highlight-box {
            background: #f5f0ff;
            border: 1px solid #8B5CF6;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <table style="border: none; margin-bottom: 40px;">
        <tr>
            <td style="border: none; padding: 0;">
                <div class="logo">GlamourSchedule</div>
                <div style="color: #666; margin-top: 5px;">Sales Partner Programma</div>
            </td>
            <td style="border: none; padding: 0; text-align: right;">
                <div class="invoice-title">COMMISSIE FACTUUR</div>
                <div><strong>Factuurnummer:</strong> {$invoiceNumber}</div>
                <div><strong>Factuurdatum:</strong> {$invoiceDate}</div>
            </td>
        </tr>
    </table>

    <div class="section">
        <div class="section-title">Factuur voor</div>
        <div class="address">
            <strong>{$salesName}</strong><br>
            {$salesEmail}
        </div>
    </div>

    <div class="section">
        <div class="section-title">Commissieoverzicht</div>
        <p>Overzicht van geconverteerde salons waarvoor commissie wordt uitbetaald.</p>
        <table>
            <thead>
                <tr>
                    <th>Salon</th>
                    <th>Registratiedatum</th>
                    <th class="right">Commissie</th>
                </tr>
            </thead>
            <tbody>
                {$referralsHtml}
            </tbody>
        </table>
    </div>

    <div class="totals">
        <table style="width: 300px; margin-left: auto;">
            <tr>
                <td style="border: none;">Aantal salons</td>
                <td style="border: none; text-align: right;">{$referralCount}</td>
            </tr>
            <tr style="border-top: 2px solid #8B5CF6;">
                <td style="border: none; font-weight: bold; font-size: 14px; color: #8B5CF6; padding-top: 10px;">Totaal commissie</td>
                <td style="border: none; font-weight: bold; font-size: 14px; color: #8B5CF6; text-align: right; padding-top: 10px;">&euro;{$totalCommission}</td>
            </tr>
        </table>
    </div>

    <div class="highlight-box">
        <strong>Bedankt voor je bijdrage aan GlamourSchedule!</strong><br>
        De uitbetaling wordt binnen 1-2 werkdagen op je rekening bijgeschreven.
    </div>

    <div class="footer">
        <p>
            GlamourSchedule B.V. | KvK: 12345678 | BTW: NL123456789B01<br>
            Dit is een automatisch gegenereerde factuur. Voor vragen kunt u contact opnemen via info@glamourschedule.com
        </p>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Get invoice file path by invoice number
     */
    public function getInvoicePath(string $invoiceNumber): ?string
    {
        $filename = "factuur-{$invoiceNumber}.pdf";
        $filepath = $this->storagePath . '/' . $filename;

        return file_exists($filepath) ? $filepath : null;
    }

    /**
     * Delete old invoices (older than 7 years for Dutch tax requirements)
     */
    public function cleanupOldInvoices(int $yearsToKeep = 7): int
    {
        $deleted = 0;
        $cutoffDate = strtotime("-{$yearsToKeep} years");

        $files = glob($this->storagePath . '/factuur-*.pdf');
        foreach ($files as $file) {
            if (filemtime($file) < $cutoffDate) {
                unlink($file);
                $deleted++;
            }
        }

        return $deleted;
    }
}
