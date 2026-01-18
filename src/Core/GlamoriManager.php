<?php
/**
 * GlamoriManager - AI Manager voor Bedrijven
 *
 * Beheert herinneringen, statistieken, proactieve tips en automatische meldingen
 * voor salons en bedrijven op het GlamourSchedule platform.
 */

namespace GlamourSchedule\Core;

class GlamoriManager
{
    private Database $db;
    private ?Mailer $mailer;
    private string $language;

    // Teksten per taal
    private array $texts = [
        'nl' => [
            'greeting_morning' => 'Goedemorgen',
            'greeting_afternoon' => 'Goedemiddag',
            'greeting_evening' => 'Goedenavond',
            'bookings_today' => 'Je hebt vandaag %d afspraken',
            'no_bookings_today' => 'Je hebt vandaag geen afspraken',
            'revenue_this_week' => 'Omzet deze week: %s',
            'revenue_this_month' => 'Omzet deze maand: %s',
            'new_reviews' => 'Je hebt %d nieuwe review(s) om te beantwoorden',
            'pending_payouts' => 'Er staan %d uitbetalingen klaar',
            'empty_slots_tip' => 'Tip: Je hebt %d lege slots deze week. Overweeg een actie!',
            'popular_service' => 'Je populairste dienst deze maand: %s',
            'new_customers' => '%d nieuwe klanten deze maand',
            'returning_customers' => '%d terugkerende klanten deze maand',
            'review_reminder' => 'Vergeet niet de review van %s te beantwoorden',
            'trial_ending' => 'Let op: Je proefperiode eindigt over %d dagen',
            'subscription_reminder' => 'Je abonnement moet verlengd worden',
            'daily_summary_subject' => 'Je dagelijkse samenvatting - GlamourSchedule',
            'weekly_summary_subject' => 'Je wekelijkse samenvatting - GlamourSchedule',
            'tip_slow_day' => 'Vandaag is een rustige dag - perfecte tijd om je profiel te updaten!',
            'tip_no_photos' => 'Tip: Salons met foto\'s krijgen 3x meer boekingen',
            'tip_incomplete_profile' => 'Maak je profiel compleet voor meer zichtbaarheid',
            'tip_respond_reviews' => 'Reageer op reviews om je rating te verbeteren',
            'tip_add_services' => 'Voeg meer diensten toe om meer klanten aan te trekken',
            'booking_reminder' => 'Herinnering: %s heeft morgen een afspraak om %s',
            'payout_processed' => 'Je uitbetaling van %s is onderweg',
            'new_booking_alert' => 'Nieuwe boeking: %s op %s om %s',
            'cancellation_alert' => 'Annulering: %s heeft de afspraak van %s geannuleerd',
            'rating_improved' => 'Gefeliciteerd! Je rating is gestegen naar %s sterren',
            'milestone_bookings' => 'Milestone bereikt: %d boekingen via GlamourSchedule!'
        ],
        'en' => [
            'greeting_morning' => 'Good morning',
            'greeting_afternoon' => 'Good afternoon',
            'greeting_evening' => 'Good evening',
            'bookings_today' => 'You have %d appointments today',
            'no_bookings_today' => 'You have no appointments today',
            'revenue_this_week' => 'Revenue this week: %s',
            'revenue_this_month' => 'Revenue this month: %s',
            'new_reviews' => 'You have %d new review(s) to respond to',
            'pending_payouts' => '%d payouts are ready',
            'empty_slots_tip' => 'Tip: You have %d empty slots this week. Consider a promotion!',
            'popular_service' => 'Your most popular service this month: %s',
            'new_customers' => '%d new customers this month',
            'returning_customers' => '%d returning customers this month',
            'review_reminder' => 'Don\'t forget to respond to the review from %s',
            'trial_ending' => 'Notice: Your trial ends in %d days',
            'subscription_reminder' => 'Your subscription needs to be renewed',
            'daily_summary_subject' => 'Your daily summary - GlamourSchedule',
            'weekly_summary_subject' => 'Your weekly summary - GlamourSchedule',
            'tip_slow_day' => 'Today is a slow day - perfect time to update your profile!',
            'tip_no_photos' => 'Tip: Salons with photos get 3x more bookings',
            'tip_incomplete_profile' => 'Complete your profile for more visibility',
            'tip_respond_reviews' => 'Respond to reviews to improve your rating',
            'tip_add_services' => 'Add more services to attract more customers',
            'booking_reminder' => 'Reminder: %s has an appointment tomorrow at %s',
            'payout_processed' => 'Your payout of %s is on its way',
            'new_booking_alert' => 'New booking: %s on %s at %s',
            'cancellation_alert' => 'Cancellation: %s cancelled the appointment on %s',
            'rating_improved' => 'Congratulations! Your rating has increased to %s stars',
            'milestone_bookings' => 'Milestone reached: %d bookings via GlamourSchedule!'
        ],
        'de' => [
            'greeting_morning' => 'Guten Morgen',
            'greeting_afternoon' => 'Guten Tag',
            'greeting_evening' => 'Guten Abend',
            'bookings_today' => 'Du hast heute %d Termine',
            'no_bookings_today' => 'Du hast heute keine Termine',
            'revenue_this_week' => 'Umsatz diese Woche: %s',
            'revenue_this_month' => 'Umsatz diesen Monat: %s',
            'new_reviews' => 'Du hast %d neue Bewertung(en) zu beantworten',
            'pending_payouts' => '%d Auszahlungen stehen bereit',
            'empty_slots_tip' => 'Tipp: Du hast %d freie Termine diese Woche. Eine Aktion starten!',
            'popular_service' => 'Dein beliebtester Service diesen Monat: %s',
            'new_customers' => '%d neue Kunden diesen Monat',
            'returning_customers' => '%d wiederkehrende Kunden diesen Monat',
            'review_reminder' => 'Vergiss nicht, auf die Bewertung von %s zu antworten',
            'trial_ending' => 'Achtung: Deine Testphase endet in %d Tagen',
            'subscription_reminder' => 'Dein Abonnement muss verlangert werden',
            'daily_summary_subject' => 'Deine tagliche Zusammenfassung - GlamourSchedule',
            'weekly_summary_subject' => 'Deine wochentliche Zusammenfassung - GlamourSchedule',
            'tip_slow_day' => 'Heute ist ein ruhiger Tag - perfekt um dein Profil zu aktualisieren!',
            'tip_no_photos' => 'Tipp: Salons mit Fotos bekommen 3x mehr Buchungen',
            'tip_incomplete_profile' => 'Vervollstandige dein Profil fur mehr Sichtbarkeit',
            'tip_respond_reviews' => 'Antworte auf Bewertungen um deine Bewertung zu verbessern',
            'tip_add_services' => 'Fuge mehr Services hinzu um mehr Kunden anzuziehen',
            'booking_reminder' => 'Erinnerung: %s hat morgen einen Termin um %s',
            'payout_processed' => 'Deine Auszahlung von %s ist unterwegs',
            'new_booking_alert' => 'Neue Buchung: %s am %s um %s',
            'cancellation_alert' => 'Stornierung: %s hat den Termin am %s storniert',
            'rating_improved' => 'Herzlichen Gluckwunsch! Deine Bewertung ist auf %s Sterne gestiegen',
            'milestone_bookings' => 'Meilenstein erreicht: %d Buchungen uber GlamourSchedule!'
        ],
        'fr' => [
            'greeting_morning' => 'Bonjour',
            'greeting_afternoon' => 'Bon apres-midi',
            'greeting_evening' => 'Bonsoir',
            'bookings_today' => 'Vous avez %d rendez-vous aujourd\'hui',
            'no_bookings_today' => 'Vous n\'avez pas de rendez-vous aujourd\'hui',
            'revenue_this_week' => 'Revenus cette semaine: %s',
            'revenue_this_month' => 'Revenus ce mois: %s',
            'new_reviews' => 'Vous avez %d nouvel(s) avis a repondre',
            'pending_payouts' => '%d paiements sont prets',
            'empty_slots_tip' => 'Conseil: Vous avez %d creneaux vides cette semaine. Lancez une promotion!',
            'popular_service' => 'Votre service le plus populaire ce mois: %s',
            'new_customers' => '%d nouveaux clients ce mois',
            'returning_customers' => '%d clients fideles ce mois',
            'review_reminder' => 'N\'oubliez pas de repondre a l\'avis de %s',
            'trial_ending' => 'Attention: Votre essai se termine dans %d jours',
            'subscription_reminder' => 'Votre abonnement doit etre renouvele',
            'daily_summary_subject' => 'Votre resume quotidien - GlamourSchedule',
            'weekly_summary_subject' => 'Votre resume hebdomadaire - GlamourSchedule',
            'tip_slow_day' => 'Aujourd\'hui est calme - parfait pour mettre a jour votre profil!',
            'tip_no_photos' => 'Conseil: Les salons avec photos recoivent 3x plus de reservations',
            'tip_incomplete_profile' => 'Completez votre profil pour plus de visibilite',
            'tip_respond_reviews' => 'Repondez aux avis pour ameliorer votre note',
            'tip_add_services' => 'Ajoutez plus de services pour attirer plus de clients',
            'booking_reminder' => 'Rappel: %s a un rendez-vous demain a %s',
            'payout_processed' => 'Votre paiement de %s est en route',
            'new_booking_alert' => 'Nouvelle reservation: %s le %s a %s',
            'cancellation_alert' => 'Annulation: %s a annule le rendez-vous du %s',
            'rating_improved' => 'Felicitations! Votre note est passee a %s etoiles',
            'milestone_bookings' => 'Jalon atteint: %d reservations via GlamourSchedule!'
        ]
    ];

    public function __construct(Database $db, ?Mailer $mailer = null, string $language = 'nl')
    {
        $this->db = $db;
        $this->mailer = $mailer;
        $this->language = $language;
    }

    /**
     * Haal tekst op in de juiste taal
     */
    private function getText(string $key, ...$args): string
    {
        $text = $this->texts[$this->language][$key] ?? $this->texts['nl'][$key] ?? $key;
        if (!empty($args)) {
            return sprintf($text, ...$args);
        }
        return $text;
    }

    /**
     * Krijg de juiste begroeting gebaseerd op tijd
     */
    private function getGreeting(): string
    {
        $hour = (int)date('H');
        if ($hour < 12) {
            return $this->getText('greeting_morning');
        } elseif ($hour < 18) {
            return $this->getText('greeting_afternoon');
        }
        return $this->getText('greeting_evening');
    }

    // =========================================================================
    // STATISTIEKEN
    // =========================================================================

    /**
     * Haal alle statistieken op voor een bedrijf
     */
    public function getBusinessStats(int $businessId): array
    {
        return [
            'today' => $this->getTodayStats($businessId),
            'week' => $this->getWeekStats($businessId),
            'month' => $this->getMonthStats($businessId),
            'customers' => $this->getCustomerStats($businessId),
            'services' => $this->getServiceStats($businessId),
            'reviews' => $this->getReviewStats($businessId)
        ];
    }

    /**
     * Statistieken van vandaag
     */
    public function getTodayStats(int $businessId): array
    {
        $today = date('Y-m-d');

        // Boekingen vandaag
        $bookings = $this->db->query(
            "SELECT COUNT(*) as total,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
                    SUM(CASE WHEN status IN ('pending', 'confirmed') THEN 1 ELSE 0 END) as upcoming,
                    SUM(CASE WHEN status = 'completed' THEN total_price ELSE 0 END) as revenue
             FROM bookings
             WHERE business_id = ? AND appointment_date = ?",
            [$businessId, $today]
        )->fetch();

        return [
            'total_bookings' => (int)($bookings['total'] ?? 0),
            'completed' => (int)($bookings['completed'] ?? 0),
            'cancelled' => (int)($bookings['cancelled'] ?? 0),
            'upcoming' => (int)($bookings['upcoming'] ?? 0),
            'revenue' => (float)($bookings['revenue'] ?? 0)
        ];
    }

    /**
     * Statistieken van deze week
     */
    public function getWeekStats(int $businessId): array
    {
        $startOfWeek = date('Y-m-d', strtotime('monday this week'));
        $endOfWeek = date('Y-m-d', strtotime('sunday this week'));

        $bookings = $this->db->query(
            "SELECT COUNT(*) as total,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
                    SUM(CASE WHEN status = 'completed' THEN total_price ELSE 0 END) as revenue
             FROM bookings
             WHERE business_id = ?
             AND appointment_date BETWEEN ? AND ?",
            [$businessId, $startOfWeek, $endOfWeek]
        )->fetch();

        // Vergelijk met vorige week
        $prevStart = date('Y-m-d', strtotime('-1 week', strtotime($startOfWeek)));
        $prevEnd = date('Y-m-d', strtotime('-1 week', strtotime($endOfWeek)));

        $prevBookings = $this->db->query(
            "SELECT COUNT(*) as total,
                    SUM(CASE WHEN status = 'completed' THEN total_price ELSE 0 END) as revenue
             FROM bookings
             WHERE business_id = ?
             AND appointment_date BETWEEN ? AND ?",
            [$businessId, $prevStart, $prevEnd]
        )->fetch();

        $currentRevenue = (float)($bookings['revenue'] ?? 0);
        $prevRevenue = (float)($prevBookings['revenue'] ?? 0);
        $revenueChange = $prevRevenue > 0 ? (($currentRevenue - $prevRevenue) / $prevRevenue) * 100 : 0;

        return [
            'total_bookings' => (int)($bookings['total'] ?? 0),
            'completed' => (int)($bookings['completed'] ?? 0),
            'cancelled' => (int)($bookings['cancelled'] ?? 0),
            'revenue' => $currentRevenue,
            'revenue_change_percent' => round($revenueChange, 1),
            'prev_week_revenue' => $prevRevenue
        ];
    }

    /**
     * Statistieken van deze maand
     */
    public function getMonthStats(int $businessId): array
    {
        $startOfMonth = date('Y-m-01');
        $endOfMonth = date('Y-m-t');

        $bookings = $this->db->query(
            "SELECT COUNT(*) as total,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
                    SUM(CASE WHEN status = 'no_show' THEN 1 ELSE 0 END) as no_shows,
                    SUM(CASE WHEN status = 'completed' THEN total_price ELSE 0 END) as revenue,
                    SUM(CASE WHEN status = 'completed' THEN payout_amount ELSE 0 END) as net_revenue
             FROM bookings
             WHERE business_id = ?
             AND appointment_date BETWEEN ? AND ?",
            [$businessId, $startOfMonth, $endOfMonth]
        )->fetch();

        // Vergelijk met vorige maand
        $prevStart = date('Y-m-01', strtotime('-1 month'));
        $prevEnd = date('Y-m-t', strtotime('-1 month'));

        $prevBookings = $this->db->query(
            "SELECT COUNT(*) as total,
                    SUM(CASE WHEN status = 'completed' THEN total_price ELSE 0 END) as revenue
             FROM bookings
             WHERE business_id = ?
             AND appointment_date BETWEEN ? AND ?",
            [$businessId, $prevStart, $prevEnd]
        )->fetch();

        $currentRevenue = (float)($bookings['revenue'] ?? 0);
        $prevRevenue = (float)($prevBookings['revenue'] ?? 0);
        $revenueChange = $prevRevenue > 0 ? (($currentRevenue - $prevRevenue) / $prevRevenue) * 100 : 0;

        return [
            'total_bookings' => (int)($bookings['total'] ?? 0),
            'completed' => (int)($bookings['completed'] ?? 0),
            'cancelled' => (int)($bookings['cancelled'] ?? 0),
            'no_shows' => (int)($bookings['no_shows'] ?? 0),
            'revenue' => $currentRevenue,
            'net_revenue' => (float)($bookings['net_revenue'] ?? 0),
            'revenue_change_percent' => round($revenueChange, 1),
            'prev_month_revenue' => $prevRevenue
        ];
    }

    /**
     * Klantstatistieken
     */
    public function getCustomerStats(int $businessId): array
    {
        $startOfMonth = date('Y-m-01');
        $endOfMonth = date('Y-m-t');

        // Unieke klanten deze maand
        $uniqueCustomers = $this->db->query(
            "SELECT COUNT(DISTINCT COALESCE(user_id, guest_email)) as total
             FROM bookings
             WHERE business_id = ?
             AND appointment_date BETWEEN ? AND ?
             AND status IN ('completed', 'confirmed', 'pending')",
            [$businessId, $startOfMonth, $endOfMonth]
        )->fetch();

        // Nieuwe vs terugkerende klanten
        $newCustomers = $this->db->query(
            "SELECT COUNT(DISTINCT customer) as total
             FROM (
                 SELECT COALESCE(user_id, guest_email) as customer,
                        MIN(appointment_date) as first_visit
                 FROM bookings
                 WHERE business_id = ?
                 AND status IN ('completed', 'confirmed', 'pending')
                 GROUP BY COALESCE(user_id, guest_email)
                 HAVING first_visit BETWEEN ? AND ?
             ) as new_customers",
            [$businessId, $startOfMonth, $endOfMonth]
        )->fetch();

        $totalUnique = (int)($uniqueCustomers['total'] ?? 0);
        $newCount = (int)($newCustomers['total'] ?? 0);
        $returningCount = max(0, $totalUnique - $newCount);

        // Top klanten (meeste boekingen)
        $topCustomers = $this->db->query(
            "SELECT COALESCE(CONCAT(u.first_name, ' ', u.last_name), b.guest_name) as name,
                    COUNT(*) as booking_count,
                    SUM(b.total_price) as total_spent
             FROM bookings b
             LEFT JOIN users u ON b.user_id = u.id
             WHERE b.business_id = ?
             AND b.status = 'completed'
             GROUP BY COALESCE(b.user_id, b.guest_email)
             ORDER BY booking_count DESC
             LIMIT 5",
            [$businessId]
        )->fetchAll();

        return [
            'unique_this_month' => $totalUnique,
            'new_customers' => $newCount,
            'returning_customers' => $returningCount,
            'retention_rate' => $totalUnique > 0 ? round(($returningCount / $totalUnique) * 100, 1) : 0,
            'top_customers' => $topCustomers
        ];
    }

    /**
     * Dienststatistieken
     */
    public function getServiceStats(int $businessId): array
    {
        $startOfMonth = date('Y-m-01');
        $endOfMonth = date('Y-m-t');

        // Populairste diensten
        $popularServices = $this->db->query(
            "SELECT s.name, COUNT(*) as booking_count, SUM(b.total_price) as revenue
             FROM bookings b
             JOIN services s ON b.service_id = s.id
             WHERE b.business_id = ?
             AND b.appointment_date BETWEEN ? AND ?
             AND b.status IN ('completed', 'confirmed', 'pending')
             GROUP BY b.service_id
             ORDER BY booking_count DESC
             LIMIT 5",
            [$businessId, $startOfMonth, $endOfMonth]
        )->fetchAll();

        // Gemiddelde boekingswaarde
        $avgBooking = $this->db->query(
            "SELECT AVG(total_price) as avg_price
             FROM bookings
             WHERE business_id = ?
             AND status = 'completed'
             AND appointment_date BETWEEN ? AND ?",
            [$businessId, $startOfMonth, $endOfMonth]
        )->fetch();

        return [
            'popular_services' => $popularServices,
            'average_booking_value' => round((float)($avgBooking['avg_price'] ?? 0), 2),
            'most_popular' => $popularServices[0]['name'] ?? null
        ];
    }

    /**
     * Review statistieken
     */
    public function getReviewStats(int $businessId): array
    {
        // Algemene review stats
        $stats = $this->db->query(
            "SELECT COUNT(*) as total,
                    AVG(rating) as avg_rating,
                    SUM(CASE WHEN business_response IS NULL THEN 1 ELSE 0 END) as unanswered
             FROM reviews
             WHERE business_id = ?",
            [$businessId]
        )->fetch();

        // Reviews deze maand
        $startOfMonth = date('Y-m-01');
        $monthlyStats = $this->db->query(
            "SELECT COUNT(*) as total, AVG(rating) as avg_rating
             FROM reviews
             WHERE business_id = ?
             AND created_at >= ?",
            [$businessId, $startOfMonth]
        )->fetch();

        // Rating distributie
        $distribution = $this->db->query(
            "SELECT rating, COUNT(*) as count
             FROM reviews
             WHERE business_id = ?
             GROUP BY rating
             ORDER BY rating DESC",
            [$businessId]
        )->fetchAll();

        $ratingDistribution = [];
        foreach ($distribution as $row) {
            $ratingDistribution[$row['rating']] = (int)$row['count'];
        }

        return [
            'total_reviews' => (int)($stats['total'] ?? 0),
            'average_rating' => round((float)($stats['avg_rating'] ?? 0), 1),
            'unanswered_reviews' => (int)($stats['unanswered'] ?? 0),
            'reviews_this_month' => (int)($monthlyStats['total'] ?? 0),
            'monthly_avg_rating' => round((float)($monthlyStats['avg_rating'] ?? 0), 1),
            'rating_distribution' => $ratingDistribution
        ];
    }

    // =========================================================================
    // PROACTIEVE TIPS
    // =========================================================================

    /**
     * Genereer proactieve tips voor een bedrijf
     */
    public function getProactiveTips(int $businessId): array
    {
        $tips = [];
        $business = $this->getBusinessInfo($businessId);

        if (!$business) {
            return $tips;
        }

        // Check voor lege dagen
        $emptySlots = $this->countEmptySlotsThisWeek($businessId);
        if ($emptySlots > 10) {
            $tips[] = [
                'type' => 'empty_slots',
                'priority' => 'high',
                'message' => $this->getText('empty_slots_tip', $emptySlots),
                'action' => 'create_promotion',
                'icon' => 'calendar-x'
            ];
        }

        // Check voor onbeantwoorde reviews
        $unansweredReviews = $this->getUnansweredReviews($businessId);
        if (count($unansweredReviews) > 0) {
            $tips[] = [
                'type' => 'unanswered_reviews',
                'priority' => 'medium',
                'message' => $this->getText('new_reviews', count($unansweredReviews)),
                'action' => 'respond_reviews',
                'icon' => 'star',
                'data' => $unansweredReviews
            ];
        }

        // Check profiel compleetheid
        $profileScore = $this->calculateProfileCompleteness($business);
        if ($profileScore < 80) {
            $tips[] = [
                'type' => 'incomplete_profile',
                'priority' => 'medium',
                'message' => $this->getText('tip_incomplete_profile'),
                'action' => 'complete_profile',
                'icon' => 'user-edit',
                'data' => ['score' => $profileScore]
            ];
        }

        // Check voor foto's
        $hasPhotos = $this->businessHasPhotos($businessId);
        if (!$hasPhotos) {
            $tips[] = [
                'type' => 'no_photos',
                'priority' => 'medium',
                'message' => $this->getText('tip_no_photos'),
                'action' => 'add_photos',
                'icon' => 'camera'
            ];
        }

        // Check trial periode
        if ($business['subscription_status'] === 'trial' && $business['trial_ends_at']) {
            $daysLeft = (strtotime($business['trial_ends_at']) - time()) / 86400;
            if ($daysLeft > 0 && $daysLeft <= 7) {
                $tips[] = [
                    'type' => 'trial_ending',
                    'priority' => 'high',
                    'message' => $this->getText('trial_ending', (int)$daysLeft),
                    'action' => 'upgrade_subscription',
                    'icon' => 'clock'
                ];
            }
        }

        // Check voor weinig diensten
        $serviceCount = $this->countServices($businessId);
        if ($serviceCount < 3) {
            $tips[] = [
                'type' => 'few_services',
                'priority' => 'low',
                'message' => $this->getText('tip_add_services'),
                'action' => 'add_services',
                'icon' => 'plus-circle'
            ];
        }

        // Rustige dag tip
        $todayBookings = $this->getTodayStats($businessId)['total_bookings'];
        if ($todayBookings < 2) {
            $tips[] = [
                'type' => 'slow_day',
                'priority' => 'low',
                'message' => $this->getText('tip_slow_day'),
                'action' => 'update_profile',
                'icon' => 'coffee'
            ];
        }

        // Sorteer op prioriteit
        usort($tips, function($a, $b) {
            $priorityOrder = ['high' => 0, 'medium' => 1, 'low' => 2];
            return ($priorityOrder[$a['priority']] ?? 2) <=> ($priorityOrder[$b['priority']] ?? 2);
        });

        return $tips;
    }

    /**
     * Tel lege slots deze week
     */
    private function countEmptySlotsThisWeek(int $businessId): int
    {
        // Haal openingstijden op
        $openingHours = $this->db->query(
            "SELECT * FROM business_hours WHERE business_id = ? AND is_closed = 0",
            [$businessId]
        )->fetchAll();

        if (empty($openingHours)) {
            return 0;
        }

        // Bereken totaal beschikbare slots minus geboekte slots
        $totalSlots = 0;
        $bookedSlots = 0;

        $startOfWeek = date('Y-m-d', strtotime('monday this week'));

        for ($i = 0; $i < 7; $i++) {
            $date = date('Y-m-d', strtotime("+$i days", strtotime($startOfWeek)));
            $dayOfWeek = date('N', strtotime($date)); // 1 = Monday, 7 = Sunday

            foreach ($openingHours as $oh) {
                if ($oh['day_of_week'] == $dayOfWeek) {
                    $open = strtotime($oh['open_time']);
                    $close = strtotime($oh['close_time']);
                    $totalSlots += ($close - $open) / 1800; // 30 min slots
                }
            }
        }

        // Tel geboekte slots
        $endOfWeek = date('Y-m-d', strtotime('sunday this week'));
        $booked = $this->db->query(
            "SELECT SUM(CEIL(duration_minutes / 30)) as slots
             FROM bookings
             WHERE business_id = ?
             AND appointment_date BETWEEN ? AND ?
             AND status NOT IN ('cancelled')",
            [$businessId, $startOfWeek, $endOfWeek]
        )->fetch();

        $bookedSlots = (int)($booked['slots'] ?? 0);

        return max(0, $totalSlots - $bookedSlots);
    }

    /**
     * Haal onbeantwoorde reviews op
     */
    private function getUnansweredReviews(int $businessId): array
    {
        return $this->db->query(
            "SELECT r.*, CONCAT(u.first_name, ' ', u.last_name) as customer_name
             FROM reviews r
             LEFT JOIN users u ON r.user_id = u.id
             WHERE r.business_id = ?
             AND r.business_response IS NULL
             ORDER BY r.created_at DESC
             LIMIT 10",
            [$businessId]
        )->fetchAll();
    }

    /**
     * Bereken profiel compleetheid
     */
    private function calculateProfileCompleteness(array $business): int
    {
        $score = 0;
        $maxScore = 100;

        // Basis velden (50%)
        if (!empty($business['company_name'])) $score += 10;
        if (!empty($business['description'])) $score += 10;
        if (!empty($business['email'])) $score += 5;
        if (!empty($business['phone'])) $score += 5;
        if (!empty($business['street']) && !empty($business['city'])) $score += 10;
        if (!empty($business['kvk_number'])) $score += 10;

        // Media (20%)
        if (!empty($business['logo'])) $score += 10;
        if (!empty($business['cover_image'])) $score += 10;

        // Verificatie (20%)
        if ($business['is_verified']) $score += 10;
        if ($business['iban_verified']) $score += 10;

        // Extra (10%)
        if (!empty($business['website'])) $score += 5;
        if ($business['total_reviews'] > 0) $score += 5;

        return min($score, $maxScore);
    }

    /**
     * Check of bedrijf foto's heeft
     */
    private function businessHasPhotos(int $businessId): bool
    {
        $photos = $this->db->query(
            "SELECT COUNT(*) as count FROM business_photos WHERE business_id = ?",
            [$businessId]
        )->fetch();

        return ($photos['count'] ?? 0) > 0;
    }

    /**
     * Tel aantal diensten
     */
    private function countServices(int $businessId): int
    {
        $services = $this->db->query(
            "SELECT COUNT(*) as count FROM services WHERE business_id = ? AND is_active = 1",
            [$businessId]
        )->fetch();

        return (int)($services['count'] ?? 0);
    }

    /**
     * Haal bedrijfsinfo op
     */
    private function getBusinessInfo(int $businessId): ?array
    {
        return $this->db->query(
            "SELECT * FROM businesses WHERE id = ?",
            [$businessId]
        )->fetch() ?: null;
    }

    // =========================================================================
    // MELDINGEN EN HERINNERINGEN
    // =========================================================================

    /**
     * Genereer dagelijkse samenvatting voor een bedrijf
     */
    public function generateDailySummary(int $businessId): array
    {
        $business = $this->getBusinessInfo($businessId);
        if (!$business) {
            return [];
        }

        $this->language = $business['language'] ?? 'nl';

        $today = $this->getTodayStats($businessId);
        $week = $this->getWeekStats($businessId);
        $tips = $this->getProactiveTips($businessId);

        // Komende afspraken vandaag
        $upcomingToday = $this->db->query(
            "SELECT b.*, s.name as service_name,
                    COALESCE(CONCAT(u.first_name, ' ', u.last_name), b.guest_name) as customer_name
             FROM bookings b
             JOIN services s ON b.service_id = s.id
             LEFT JOIN users u ON b.user_id = u.id
             WHERE b.business_id = ?
             AND b.appointment_date = CURDATE()
             AND b.status IN ('pending', 'confirmed')
             ORDER BY b.appointment_time ASC",
            [$businessId]
        )->fetchAll();

        return [
            'greeting' => $this->getGreeting(),
            'business_name' => $business['company_name'],
            'date' => date('d-m-Y'),
            'today_summary' => $today['total_bookings'] > 0
                ? $this->getText('bookings_today', $today['total_bookings'])
                : $this->getText('no_bookings_today'),
            'today_stats' => $today,
            'week_stats' => $week,
            'upcoming_appointments' => $upcomingToday,
            'tips' => array_slice($tips, 0, 3), // Max 3 tips
            'subject' => $this->getText('daily_summary_subject')
        ];
    }

    /**
     * Verstuur dagelijkse samenvatting email
     */
    public function sendDailySummaryEmail(int $businessId): bool
    {
        if (!$this->mailer) {
            return false;
        }

        $summary = $this->generateDailySummary($businessId);
        if (empty($summary)) {
            return false;
        }

        $business = $this->getBusinessInfo($businessId);
        $owner = $this->db->query(
            "SELECT * FROM users WHERE id = ?",
            [$business['user_id']]
        )->fetch();

        if (!$owner || empty($owner['email'])) {
            return false;
        }

        // Genereer email HTML
        $html = $this->renderDailySummaryEmail($summary);

        return $this->mailer->send(
            $owner['email'],
            $summary['subject'],
            $html
        );
    }

    /**
     * Render dagelijkse samenvatting email HTML
     */
    private function renderDailySummaryEmail(array $summary): string
    {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white; padding: 20px; border-radius: 8px 8px 0 0; }
                .content { background: #f9fafb; padding: 20px; }
                .stat-box { background: white; padding: 15px; border-radius: 8px; margin: 10px 0; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
                .stat-number { font-size: 24px; font-weight: bold; color: #6366f1; }
                .tip { background: #fef3c7; border-left: 4px solid #f59e0b; padding: 10px; margin: 10px 0; }
                .appointment { background: white; padding: 10px; margin: 5px 0; border-radius: 4px; }
                .footer { text-align: center; padding: 20px; color: #6b7280; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>' . htmlspecialchars($summary['greeting']) . ', ' . htmlspecialchars($summary['business_name']) . '!</h1>
                    <p>' . htmlspecialchars($summary['date']) . '</p>
                </div>
                <div class="content">
                    <h2>' . htmlspecialchars($summary['today_summary']) . '</h2>';

        // Statistieken
        $html .= '
                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <div class="stat-box" style="flex: 1; min-width: 120px;">
                            <div class="stat-number">' . $summary['today_stats']['total_bookings'] . '</div>
                            <div>Afspraken vandaag</div>
                        </div>
                        <div class="stat-box" style="flex: 1; min-width: 120px;">
                            <div class="stat-number">' . number_format($summary['week_stats']['revenue'], 2, ',', '.') . '</div>
                            <div>Omzet deze week</div>
                        </div>
                    </div>';

        // Komende afspraken
        if (!empty($summary['upcoming_appointments'])) {
            $html .= '<h3>Komende afspraken</h3>';
            foreach ($summary['upcoming_appointments'] as $apt) {
                $html .= '
                    <div class="appointment">
                        <strong>' . date('H:i', strtotime($apt['appointment_time'])) . '</strong> -
                        ' . htmlspecialchars($apt['customer_name']) . ' -
                        ' . htmlspecialchars($apt['service_name']) . '
                    </div>';
            }
        }

        // Tips
        if (!empty($summary['tips'])) {
            $html .= '<h3>Tips voor je</h3>';
            foreach ($summary['tips'] as $tip) {
                $html .= '<div class="tip">' . htmlspecialchars($tip['message']) . '</div>';
            }
        }

        $html .= '
                </div>
                <div class="footer">
                    <p>GlamourSchedule - Jouw salon management platform</p>
                    <p><a href="https://glamourschedule.nl/business/dashboard">Ga naar je dashboard</a></p>
                </div>
            </div>
        </body>
        </html>';

        return $html;
    }

    /**
     * Stuur herinnering voor morgen's afspraken
     */
    public function sendTomorrowReminders(int $businessId): int
    {
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        $business = $this->getBusinessInfo($businessId);

        if (!$business) {
            return 0;
        }

        $this->language = $business['language'] ?? 'nl';

        // Haal morgen's afspraken op
        $appointments = $this->db->query(
            "SELECT b.*, s.name as service_name,
                    COALESCE(CONCAT(u.first_name, ' ', u.last_name), b.guest_name) as customer_name
             FROM bookings b
             JOIN services s ON b.service_id = s.id
             LEFT JOIN users u ON b.user_id = u.id
             WHERE b.business_id = ?
             AND b.appointment_date = ?
             AND b.status IN ('pending', 'confirmed')",
            [$businessId, $tomorrow]
        )->fetchAll();

        $sentCount = 0;

        foreach ($appointments as $apt) {
            // Sla melding op in database
            $this->createNotification(
                $businessId,
                'booking_reminder',
                $this->getText('booking_reminder', $apt['customer_name'], date('H:i', strtotime($apt['appointment_time']))),
                ['booking_id' => $apt['id']]
            );
            $sentCount++;
        }

        return $sentCount;
    }

    /**
     * Maak een melding aan
     */
    public function createNotification(int $businessId, string $type, string $message, array $data = []): int
    {
        // Check of tabel bestaat, zo niet maak aan
        $this->ensureNotificationsTableExists();

        $this->db->query(
            "INSERT INTO glamori_notifications (business_id, type, message, data, created_at)
             VALUES (?, ?, ?, ?, NOW())",
            [$businessId, $type, $message, json_encode($data)]
        );

        return $this->db->lastInsertId();
    }

    /**
     * Haal meldingen op voor een bedrijf
     */
    public function getNotifications(int $businessId, int $limit = 20, bool $unreadOnly = false): array
    {
        $this->ensureNotificationsTableExists();

        $sql = "SELECT * FROM glamori_notifications WHERE business_id = ?";
        $params = [$businessId];

        if ($unreadOnly) {
            $sql .= " AND read_at IS NULL";
        }

        $sql .= " ORDER BY created_at DESC LIMIT ?";
        $params[] = $limit;

        return $this->db->query($sql, $params)->fetchAll();
    }

    /**
     * Markeer melding als gelezen
     */
    public function markNotificationRead(int $notificationId): bool
    {
        $this->db->query(
            "UPDATE glamori_notifications SET read_at = NOW() WHERE id = ?",
            [$notificationId]
        );
        return true;
    }

    /**
     * Zorg dat de notifications tabel bestaat
     */
    private function ensureNotificationsTableExists(): void
    {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS glamori_notifications (
                id INT AUTO_INCREMENT PRIMARY KEY,
                business_id INT NOT NULL,
                type VARCHAR(50) NOT NULL,
                message TEXT NOT NULL,
                data JSON,
                read_at TIMESTAMP NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_business (business_id),
                INDEX idx_type (type),
                INDEX idx_read (read_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }

    // =========================================================================
    // AUTOMATISCHE ALERTS
    // =========================================================================

    /**
     * Check voor nieuwe boekingen en stuur alert
     */
    public function checkNewBookings(int $businessId, int $sinceMinutes = 60): array
    {
        $since = date('Y-m-d H:i:s', strtotime("-$sinceMinutes minutes"));

        $newBookings = $this->db->query(
            "SELECT b.*, s.name as service_name,
                    COALESCE(CONCAT(u.first_name, ' ', u.last_name), b.guest_name) as customer_name
             FROM bookings b
             JOIN services s ON b.service_id = s.id
             LEFT JOIN users u ON b.user_id = u.id
             WHERE b.business_id = ?
             AND b.created_at >= ?
             ORDER BY b.created_at DESC",
            [$businessId, $since]
        )->fetchAll();

        $alerts = [];
        foreach ($newBookings as $booking) {
            $alerts[] = [
                'type' => 'new_booking',
                'message' => $this->getText(
                    'new_booking_alert',
                    $booking['customer_name'],
                    date('d-m', strtotime($booking['appointment_date'])),
                    date('H:i', strtotime($booking['appointment_time']))
                ),
                'data' => $booking
            ];
        }

        return $alerts;
    }

    /**
     * Check voor annuleringen en stuur alert
     */
    public function checkCancellations(int $businessId, int $sinceMinutes = 60): array
    {
        $since = date('Y-m-d H:i:s', strtotime("-$sinceMinutes minutes"));

        $cancellations = $this->db->query(
            "SELECT b.*, s.name as service_name,
                    COALESCE(CONCAT(u.first_name, ' ', u.last_name), b.guest_name) as customer_name
             FROM bookings b
             JOIN services s ON b.service_id = s.id
             LEFT JOIN users u ON b.user_id = u.id
             WHERE b.business_id = ?
             AND b.status = 'cancelled'
             AND b.cancelled_at >= ?
             ORDER BY b.cancelled_at DESC",
            [$businessId, $since]
        )->fetchAll();

        $alerts = [];
        foreach ($cancellations as $booking) {
            $alerts[] = [
                'type' => 'cancellation',
                'message' => $this->getText(
                    'cancellation_alert',
                    $booking['customer_name'],
                    date('d-m', strtotime($booking['appointment_date']))
                ),
                'data' => $booking
            ];
        }

        return $alerts;
    }

    /**
     * Check voor nieuwe reviews
     */
    public function checkNewReviews(int $businessId, int $sinceDays = 1): array
    {
        $since = date('Y-m-d', strtotime("-$sinceDays days"));

        $reviews = $this->db->query(
            "SELECT r.*, CONCAT(u.first_name, ' ', u.last_name) as customer_name
             FROM reviews r
             LEFT JOIN users u ON r.user_id = u.id
             WHERE r.business_id = ?
             AND r.created_at >= ?
             ORDER BY r.created_at DESC",
            [$businessId, $since]
        )->fetchAll();

        return $reviews;
    }

    /**
     * Check milestones
     */
    public function checkMilestones(int $businessId): ?array
    {
        $totalBookings = $this->db->query(
            "SELECT COUNT(*) as total FROM bookings WHERE business_id = ? AND status = 'completed'",
            [$businessId]
        )->fetch();

        $total = (int)($totalBookings['total'] ?? 0);

        // Milestone nummers
        $milestones = [10, 25, 50, 100, 250, 500, 1000, 2500, 5000];

        foreach ($milestones as $milestone) {
            if ($total === $milestone) {
                return [
                    'type' => 'milestone',
                    'milestone' => $milestone,
                    'message' => $this->getText('milestone_bookings', $milestone)
                ];
            }
        }

        return null;
    }

    // =========================================================================
    // DASHBOARD WIDGET DATA
    // =========================================================================

    /**
     * Haal alle data op voor de AI Manager widget
     */
    public function getWidgetData(int $businessId): array
    {
        $business = $this->getBusinessInfo($businessId);
        if (!$business) {
            return [];
        }

        $this->language = $business['language'] ?? 'nl';

        return [
            'greeting' => $this->getGreeting() . ', ' . explode(' ', $business['company_name'])[0] . '!',
            'today' => $this->getTodayStats($businessId),
            'week' => $this->getWeekStats($businessId),
            'month' => $this->getMonthStats($businessId),
            'tips' => array_slice($this->getProactiveTips($businessId), 0, 3),
            'notifications' => $this->getNotifications($businessId, 5, true),
            'unanswered_reviews' => count($this->getUnansweredReviews($businessId)),
            'popular_service' => $this->getServiceStats($businessId)['most_popular']
        ];
    }

    // =========================================================================
    // CRON METHODES
    // =========================================================================

    /**
     * Verwerk dagelijkse taken voor alle bedrijven
     */
    public function processDailyTasks(): array
    {
        $results = [
            'summaries_sent' => 0,
            'reminders_sent' => 0,
            'notifications_created' => 0,
            'errors' => []
        ];

        // Haal actieve bedrijven op
        $businesses = $this->db->query(
            "SELECT id, company_name, language FROM businesses
             WHERE status = 'active'
             AND subscription_status IN ('active', 'trial')"
        )->fetchAll();

        foreach ($businesses as $business) {
            try {
                $this->language = $business['language'] ?? 'nl';

                // Stuur dagelijkse samenvatting (alleen op werkdagen)
                $dayOfWeek = date('N');
                if ($dayOfWeek <= 5) { // Ma-Vr
                    if ($this->sendDailySummaryEmail($business['id'])) {
                        $results['summaries_sent']++;
                    }
                }

                // Stuur herinneringen voor morgen
                $results['reminders_sent'] += $this->sendTomorrowReminders($business['id']);

                // Check voor nieuwe reviews en maak meldingen
                $newReviews = $this->checkNewReviews($business['id']);
                foreach ($newReviews as $review) {
                    if (empty($review['business_response'])) {
                        $customerName = $review['customer_name'] ?? 'Klant';
                        $this->createNotification(
                            $business['id'],
                            'new_review',
                            $this->getText('review_reminder', $customerName),
                            ['review_id' => $review['id'], 'rating' => $review['rating']]
                        );
                        $results['notifications_created']++;
                    }
                }

                // Check milestones
                $milestone = $this->checkMilestones($business['id']);
                if ($milestone) {
                    $this->createNotification(
                        $business['id'],
                        'milestone',
                        $milestone['message'],
                        $milestone
                    );
                    $results['notifications_created']++;
                }

            } catch (\Exception $e) {
                $results['errors'][] = [
                    'business_id' => $business['id'],
                    'error' => $e->getMessage()
                ];
            }
        }

        return $results;
    }
}
