<?php
namespace GlamourSchedule\Core;

/**
 * Glamori - AI Support Chatbot
 * Handles customer support, registration help, and admin assistance
 */
class Glamori
{
    private Database $db;
    private string $language;
    private ?int $userId;
    private ?int $businessId;
    private string $sessionId;
    private ?int $conversationId = null;

    // Bot personality
    private const BOT_NAME = 'Glamori';
    private const BOT_AVATAR = '/images/glamori-avatar.png';

    public function __construct(Database $db, string $language = 'nl', ?int $userId = null, ?int $businessId = null)
    {
        $this->db = $db;
        $this->language = $language;
        $this->userId = $userId;
        $this->businessId = $businessId;
        $this->sessionId = $this->getOrCreateSessionId();
    }

    /**
     * Get or create session ID for tracking conversations
     */
    private function getOrCreateSessionId(): string
    {
        if (isset($_SESSION['glamori_session'])) {
            return $_SESSION['glamori_session'];
        }

        $sessionId = bin2hex(random_bytes(32));
        $_SESSION['glamori_session'] = $sessionId;
        return $sessionId;
    }

    /**
     * Process user message and generate response
     */
    public function chat(string $message): array
    {
        $message = trim($message);

        if (empty($message)) {
            return $this->formatResponse("Ik heb je bericht niet ontvangen. Kun je het opnieuw proberen?");
        }

        // Get or create conversation
        $this->ensureConversation();

        // Save user message
        $this->saveMessage('user', $message);

        // Detect intent
        $intent = $this->detectIntent($message);

        // Generate response based on intent
        $response = $this->generateResponse($intent, $message);

        // Save assistant response
        $this->saveMessage('assistant', $response['message'], $intent['key'], $intent['confidence']);

        return $response;
    }

    /**
     * Ensure conversation exists
     */
    private function ensureConversation(): void
    {
        // Check for existing active conversation
        $stmt = $this->db->query(
            "SELECT id FROM glamori_conversations
             WHERE session_id = ? AND status = 'active'
             ORDER BY created_at DESC LIMIT 1",
            [$this->sessionId]
        );

        $existing = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($existing) {
            $this->conversationId = (int)$existing['id'];
            return;
        }

        // Create new conversation
        $context = json_encode([
            'language' => $this->language,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? ''
        ]);

        $this->db->query(
            "INSERT INTO glamori_conversations (session_id, user_id, business_id, context)
             VALUES (?, ?, ?, ?)",
            [$this->sessionId, $this->userId, $this->businessId, $context]
        );

        $this->conversationId = (int)$this->db->lastInsertId();
    }

    /**
     * Save message to database
     */
    private function saveMessage(string $role, string $message, ?string $intent = null, ?float $confidence = null): void
    {
        $this->db->query(
            "INSERT INTO glamori_messages (conversation_id, role, message, intent, confidence)
             VALUES (?, ?, ?, ?, ?)",
            [$this->conversationId, $role, $message, $intent, $confidence]
        );
    }

    /**
     * Detect intent from message
     */
    private function detectIntent(string $message): array
    {
        $message = mb_strtolower($message);

        // Get all active intents for the current language
        $stmt = $this->db->query(
            "SELECT * FROM glamori_intents
             WHERE language = ? AND is_active = 1
             ORDER BY priority DESC",
            [$this->language]
        );

        $intents = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $bestMatch = null;
        $highestScore = 0;

        foreach ($intents as $intent) {
            $patterns = json_decode($intent['patterns'], true) ?? [];

            foreach ($patterns as $pattern) {
                // Skip fallback pattern for now
                if ($pattern === '*') continue;

                $score = $this->calculateMatchScore($message, mb_strtolower($pattern));

                if ($score > $highestScore) {
                    $highestScore = $score;
                    $bestMatch = $intent;
                }
            }
        }

        // If no good match found, use fallback
        if ($highestScore < 0.5 || !$bestMatch) {
            $stmt = $this->db->query(
                "SELECT * FROM glamori_intents WHERE intent_key = 'fallback' AND language = ?",
                [$this->language]
            );
            $bestMatch = $stmt->fetch(\PDO::FETCH_ASSOC);
            $highestScore = 0.1;
        }

        return [
            'key' => $bestMatch['intent_key'] ?? 'fallback',
            'confidence' => $highestScore,
            'responses' => json_decode($bestMatch['responses'] ?? '[]', true),
            'action' => $bestMatch['action'] ?? null
        ];
    }

    /**
     * Calculate match score between message and pattern
     */
    private function calculateMatchScore(string $message, string $pattern): float
    {
        // Exact match
        if ($message === $pattern) {
            return 1.0;
        }

        // Contains pattern
        if (strpos($message, $pattern) !== false) {
            return 0.8;
        }

        // Word-level matching
        $messageWords = explode(' ', $message);
        $patternWords = explode(' ', $pattern);

        $matchedWords = 0;
        foreach ($patternWords as $pWord) {
            foreach ($messageWords as $mWord) {
                if ($pWord === $mWord || levenshtein($pWord, $mWord) <= 2) {
                    $matchedWords++;
                    break;
                }
            }
        }

        if (count($patternWords) > 0) {
            return $matchedWords / count($patternWords) * 0.7;
        }

        return 0;
    }

    /**
     * Generate response based on intent
     */
    private function generateResponse(array $intent, string $originalMessage): array
    {
        $responses = $intent['responses'];

        if (empty($responses)) {
            $responses = ["Ik ben er om je te helpen! Wat kan ik voor je doen?"];
        }

        // Pick random response
        $message = $responses[array_rand($responses)];

        // Process markdown-style links to HTML
        $message = $this->processLinks($message);

        // Add contextual info if available
        $message = $this->addContextualInfo($message, $intent['key']);

        $response = [
            'message' => $message,
            'intent' => $intent['key'],
            'confidence' => $intent['confidence'],
            'bot_name' => self::BOT_NAME,
            'bot_avatar' => self::BOT_AVATAR,
            'timestamp' => date('H:i'),
            'suggestions' => $this->getSuggestions($intent['key'])
        ];

        // Add action if present
        if (!empty($intent['action'])) {
            $response['action'] = $intent['action'];
        }

        return $response;
    }

    /**
     * Process markdown links to HTML
     */
    private function processLinks(string $message): string
    {
        // Convert [text](url) to <a href="url">text</a>
        return preg_replace(
            '/\[([^\]]+)\]\(([^)]+)\)/',
            '<a href="$2" class="glamori-link">$1</a>',
            $message
        );
    }

    /**
     * Add contextual information to response
     */
    private function addContextualInfo(string $message, string $intent): string
    {
        switch ($intent) {
            case 'pricing':
                // Add current promo info
                $stmt = $this->db->query(
                    "SELECT country_name, current_registrations, max_promo_registrations
                     FROM country_promotions WHERE country_code = 'NL' AND is_active = 1"
                );
                $promo = $stmt->fetch(\PDO::FETCH_ASSOC);
                if ($promo) {
                    $spotsLeft = $promo['max_promo_registrations'] - $promo['current_registrations'];
                    $message .= " Er zijn nog {$spotsLeft} plekken beschikbaar voor de €0,99 actie!";
                }
                break;

            case 'register_business':
                // Check spots left
                $stmt = $this->db->query(
                    "SELECT SUM(max_promo_registrations - current_registrations) as spots
                     FROM country_promotions WHERE is_active = 1"
                );
                $result = $stmt->fetch(\PDO::FETCH_ASSOC);
                if ($result && $result['spots'] > 0) {
                    $message .= " Schiet op, want de plekken gaan snel!";
                }
                break;
        }

        return $message;
    }

    /**
     * Get suggestion buttons based on current intent
     */
    private function getSuggestions(string $intent): array
    {
        $suggestions = [
            'greeting' => [
                ['text' => 'Salon registreren', 'value' => 'Ik wil mijn salon registreren'],
                ['text' => 'Afspraak boeken', 'value' => 'Hoe boek ik een afspraak?'],
                ['text' => 'Prijzen bekijken', 'value' => 'Wat zijn de kosten?']
            ],
            'register_business' => [
                ['text' => 'Prijzen', 'value' => 'Wat kost het?'],
                ['text' => 'Direct starten', 'value' => 'Ik wil nu registreren']
            ],
            'pricing' => [
                ['text' => 'Registreren', 'value' => 'Ik wil registreren'],
                ['text' => 'Meer info', 'value' => 'Vertel me meer over het platform']
            ],
            'support' => [
                ['text' => 'Boeking probleem', 'value' => 'Ik heb een probleem met mijn boeking'],
                ['text' => 'Account probleem', 'value' => 'Ik kan niet inloggen'],
                ['text' => 'Betaling', 'value' => 'Vraag over betaling']
            ],
            'fallback' => [
                ['text' => 'Registreren', 'value' => 'Hoe registreer ik mijn salon?'],
                ['text' => 'Boeken', 'value' => 'Hoe boek ik een afspraak?'],
                ['text' => 'Support', 'value' => 'Ik heb hulp nodig']
            ],
            'thanks' => [
                ['text' => 'Nog een vraag', 'value' => 'Ik heb nog een vraag'],
                ['text' => 'Klaar', 'value' => 'Nee, bedankt!']
            ]
        ];

        return $suggestions[$intent] ?? $suggestions['fallback'];
    }

    /**
     * Get conversation history
     */
    public function getHistory(int $limit = 50): array
    {
        if (!$this->conversationId) {
            $this->ensureConversation();
        }

        $stmt = $this->db->query(
            "SELECT role, message, created_at
             FROM glamori_messages
             WHERE conversation_id = ?
             ORDER BY created_at ASC
             LIMIT ?",
            [$this->conversationId, $limit]
        );

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Format simple response
     */
    private function formatResponse(string $message, array $suggestions = []): array
    {
        return [
            'message' => $message,
            'intent' => 'system',
            'confidence' => 1.0,
            'bot_name' => self::BOT_NAME,
            'bot_avatar' => self::BOT_AVATAR,
            'timestamp' => date('H:i'),
            'suggestions' => $suggestions ?: $this->getSuggestions('fallback')
        ];
    }

    /**
     * Close current conversation
     */
    public function closeConversation(): void
    {
        if ($this->conversationId) {
            $this->db->query(
                "UPDATE glamori_conversations SET status = 'closed' WHERE id = ?",
                [$this->conversationId]
            );
        }
    }

    /**
     * Get welcome message
     */
    public function getWelcomeMessage(): array
    {
        $messages = [
            "Hallo! Ik ben Glamori, je digitale assistent bij GlamourSchedule. Waarmee kan ik je helpen?",
            "Hey! Welkom bij GlamourSchedule. Ik ben Glamori en help je graag. Wat kan ik voor je doen?",
            "Hi daar! Ik ben Glamori. Heb je vragen over het platform of wil je je salon aanmelden?"
        ];

        return [
            'message' => $messages[array_rand($messages)],
            'intent' => 'welcome',
            'confidence' => 1.0,
            'bot_name' => self::BOT_NAME,
            'bot_avatar' => self::BOT_AVATAR,
            'timestamp' => date('H:i'),
            'suggestions' => [
                ['text' => 'Salon registreren', 'value' => 'Ik wil mijn salon registreren'],
                ['text' => 'Afspraak boeken', 'value' => 'Hoe boek ik een afspraak?'],
                ['text' => 'Ik heb een vraag', 'value' => 'Ik heb een vraag']
            ]
        ];
    }
}
