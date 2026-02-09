<?php
namespace GlamourSchedule\Core;

use PDO;
use PDOException;

/**
 * Database Connection Class
 */
class Database
{
    private static ?PDO $instance = null;
    private static ?Database $dbInstance = null;
    private array $config;

    public function __construct(array $config = [])
    {
        $this->config = $config ?: $this->loadConfig();
    }

    /**
     * Get singleton Database instance
     */
    public static function getInstance(): Database
    {
        if (self::$dbInstance === null) {
            self::$dbInstance = new self();
        }
        return self::$dbInstance;
    }

    /**
     * Load config from file
     */
    private function loadConfig(): array
    {
        $config = require BASE_PATH . '/config/config.php';
        return $config['database'];
    }
    
    public function getConnection(): PDO
    {
        if (self::$instance === null) {
            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=%s',
                $this->config['host'],
                $this->config['port'],
                $this->config['name'],
                $this->config['charset']
            );
            
            try {
                self::$instance = new PDO(
                    $dsn,
                    $this->config['user'],
                    $this->config['pass'],
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]
                );

                // Set MySQL session timezone to match PHP timezone (Europe/Amsterdam)
                // This ensures NOW() returns the same time as PHP DateTime
                $timezone = $this->config['timezone'] ?? 'Europe/Amsterdam';
                // Validate timezone to prevent SQL injection
                $validTimezones = timezone_identifiers_list();
                if (!in_array($timezone, $validTimezones, true)) {
                    $timezone = 'Europe/Amsterdam';
                }
                $stmt = self::$instance->prepare("SET time_zone = ?");
                $stmt->execute([$timezone]);
            } catch (PDOException $e) {
                throw new \Exception('Database connection failed: ' . $e->getMessage());
            }
        }
        
        return self::$instance;
    }
    
    public function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    public function fetch(string $sql, array $params = []): ?array
    {
        $result = $this->query($sql, $params)->fetch();
        return $result ?: null;
    }
    
    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }
    
    /**
     * Validate an identifier (table or column name) to prevent SQL injection
     */
    private function validateIdentifier(string $name): string
    {
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $name)) {
            throw new \InvalidArgumentException("Invalid identifier: {$name}");
        }
        return '`' . $name . '`';
    }

    public function insert(string $table, array $data): int
    {
        $safeTable = $this->validateIdentifier($table);
        $safeColumns = array_map([$this, 'validateIdentifier'], array_keys($data));
        $columns = implode(', ', $safeColumns);
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO {$safeTable} ({$columns}) VALUES ({$placeholders})";
        $this->query($sql, array_values($data));

        return (int) $this->getConnection()->lastInsertId();
    }

    public function update(string $table, array $data, string $where, array $whereParams = []): int
    {
        $safeTable = $this->validateIdentifier($table);
        $setParts = [];
        foreach (array_keys($data) as $col) {
            $setParts[] = $this->validateIdentifier($col) . ' = ?';
        }
        $set = implode(', ', $setParts);
        $sql = "UPDATE {$safeTable} SET {$set} WHERE {$where}";

        $stmt = $this->query($sql, array_merge(array_values($data), $whereParams));
        return $stmt->rowCount();
    }

    public function lastInsertId(): int
    {
        return (int) $this->getConnection()->lastInsertId();
    }

    public function beginTransaction(): bool
    {
        return $this->getConnection()->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->getConnection()->commit();
    }

    public function rollBack(): bool
    {
        return $this->getConnection()->rollBack();
    }

    /**
     * Clean up database connection on object destruction
     */
    public function __destruct()
    {
        self::$instance = null;
    }
}
