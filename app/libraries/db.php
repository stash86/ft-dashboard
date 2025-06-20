<?php // phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

require_once __DIR__ . '/../../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

/**
 * PostgreSQL Database Library
 *
 * This file contains the PostgreSQL Database Library for FT-Dashboard,
 * providing JSONB-optimized database operations to store and retrieve
 * Freqtrade bots' data.
 *
 * PHP version 7.4+
 *
 * @category Database
 * @package  FT-Dashboard
 * @author   stash86 <stefano.ariestasia@gmail.com>
 * @license  MIT License
 * @link     https://github.com/stash86/ft-dashboard
 */
class PostgreSqlDb
{
    private $db;
    /**
     * Constructor - Initialize database connection
     *
     * @param array|null $config Optional database configuration for standalone usage
     */
    public function __construct(?array $config = null)
    {
        // Check environment variables for missing config values
        $hostname = $config['hostname'] ?? $_ENV['DB_HOST'] ??
                    getenv('DB_HOST') ?: '127.0.0.1';
        $port = $config['port'] ?? (int)($_ENV['DB_PORT'] ??
                    getenv('DB_PORT') ?: 5432);
        $database = $config['database'] ?? $_ENV['DB_NAME'] ??
                    getenv('DB_NAME') ?: 'FTdb';
        $username = $config['username'] ?? $_ENV['DB_USER'] ??
                    getenv('DB_USER') ?: 'root';
        $password = $config['password'] ?? $_ENV['DB_PASSWORD'] ??
                    getenv('DB_PASSWORD') ?: 'secret';

        $dsn = sprintf(
            'pgsql:host=%s;port=%s;dbname=%s',
            $hostname,
            $port,
            $database
        );

        $options = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false,
        ];

        $this->db = new \PDO(
            $dsn,
            $username,
            $password,
            $options
        );
    }

    /**
     * Get the raw database connection
     *
     * @return \CodeIgniter\Database\BaseConnection|\PDO
     */
    public function getDatabase()
    {
        return $this->db;
    }

    /**
     * Execute query - handles both CodeIgniter and PDO
     *
     * @param string $sql    SQL query
     * @param array  $params Parameters
     *
     * @return array
     */
    public function executeQuery(string $sql, array $params = []): array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Execute a single row query
     *
     * @param string $sql    SQL query
     * @param array  $params Parameters
     *
     * @return array|null
     */
    private function executeQuerySingle(string $sql, array $params = []): ?array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Execute an update/insert/delete query
     *
     * @param string $sql    SQL query
     * @param array  $params Parameters
     *
     * @return bool
     */
    private function executeUpdate(string $sql, array $params = []): bool
    {
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    // =====================================================
    // JSONB Query Methods (MongoDB-like interface)
    // =====================================================
    /**
     * Find one document by JSON field value
     * Similar to MongoDB's findOne()
     *
     * @param string $table    Table name
     * @param string $jsonPath JSON path (e.g., 'status', 'performance.profit')
     * @param mixed  $value    Value to search for
     *
     * @return array|null
     */
    public function findOneByJsonField(
        string $table,
        string $jsonPath,
        $value
    ): ?array {
        if (strpos($jsonPath, '.') !== false) {
            // Nested path like 'performance.profit'
            $pathParts = explode('.', $jsonPath);
            $jsonSelector = 'data';
            foreach ($pathParts as $part) {
                $jsonSelector .= "->'" . $part . "'";
            }
            $jsonSelector .= "->>0"; // Get as text for comparison
            $sql = "SELECT data FROM {$table} WHERE {$jsonSelector} = ? LIMIT 1";
        } else {
            // Simple path like 'status'
            $sql = "SELECT data FROM {$table} WHERE data->>'$jsonPath' = ? LIMIT 1";
        }

        $result = $this->executeQuerySingle($sql, [$value]);

        return $result ? json_decode($result['data'], true) : null;
    }

    /**
     * Find multiple documents by JSON field value
     * Similar to MongoDB's find()
     *
     * @param string $table    Table name
     * @param string $jsonPath JSON path
     * @param mixed  $value    Value to search for
     *
     * @return array
     */
    public function findByJsonField(string $table, string $jsonPath, $value): array
    {
        $sql = "SELECT data FROM {$table} WHERE data->>'$jsonPath' = ?";
        $results = $this->executeQuery($sql, [$value]);

        return array_map(fn ($row) => json_decode($row['data'], true), $results);
    }

    /**
     * Find one document by ID/key field
     * Similar to MongoDB's findOne(['_id' => $id])
     *
     * @param string      $table    Table name
     * @param string      $id       Document ID
     * @param string|null $keyField Key field name (default: varies by table)
     *
     * @return array|null
     */
    public function findById(
        string $table,
        string $id,
        ?string $keyField = null
    ): ?array {
        // Auto-detect key field based on table
        if ($keyField === null) {
            $keyField = match ($table) {
                'strategies' => 'strategy_name',
                default => 'id'
            };
        }

        $sql = "SELECT data FROM {$table} WHERE {$keyField} = ? LIMIT 1";
        $result = $this->executeQuerySingle($sql, [$id]);

        return $result ? json_decode($result['data'], true) : null;
    }

    /**
     * Find all documents in a table
     * Similar to MongoDB's find()
     *
     * @param string $table      Table name
     * @param array  $conditions Optional WHERE conditions
     *
     * @return array
     */
    public function findAll(string $table, array $conditions = []): array
    {
        $sql = "SELECT data FROM {$table}";
        $params = [];

        if (!empty($conditions)) {
            $whereClauses = [];
            foreach ($conditions as $field => $value) {
                if (is_array($value)) {
                    $placeholders = str_repeat('?,', count($value) - 1) . '?';
                    $whereClauses[] = "{$field} IN ({$placeholders})";
                    $params = array_merge($params, $value);
                } else {
                    $whereClauses[] = "{$field} = ?";
                    $params[] = $value;
                }
            }
            $sql .= " WHERE " . implode(' AND ', $whereClauses);
        }

        $results = $this->executeQuery($sql, $params);

        // Return raw rows (not decoded) for compatibility with existing scripts
        return $results;
    }

    /**
     * Find all documents in a table and return decoded JSON data
     * Similar to MongoDB's find() but returns decoded objects
     *
     * @param string $table      Table name
     * @param array  $conditions Optional WHERE conditions
     *
     * @return array
     */
    public function findAllDecoded(string $table, array $conditions = []): array
    {
        $results = $this->findAll($table, $conditions);
        return array_map(fn ($row) => json_decode($row['data'], true), $results);
    }

    /**
     * Insert or update JSON data
     * Similar to MongoDB's insertOne() or updateOne() with upsert
     *
     * @param string      $table    Table name
     * @param string      $id       Document ID
     * @param array       $data     JSON data to store
     * @param string|null $keyField Key field name
     *
     * @return bool
     */
    public function upsertJsonData(
        string $table,
        string $id,
        array $data,
        ?string $keyField = null
    ): bool {
        // Auto-detect key field
        if ($keyField === null) {
            $keyField = match ($table) {
                'strategies' => 'strategy_name',
                default => 'id'
            };
        }

        // Check if record exists
        $existsSql = "SELECT COUNT(*) as count FROM {$table} WHERE {$keyField} = ?";
        $existsResult = $this->executeQuerySingle($existsSql, [$id]);
        $exists = ($existsResult['count'] ?? 0) > 0;

        if ($exists) {
            // Update existing record
            $sql = "UPDATE {$table} SET data = ?, updated_at = CURRENT_TIMESTAMP " .
                   "WHERE {$keyField} = ?";
            return $this->executeUpdate($sql, [json_encode($data), $id]);
        } else {
            // Insert new record
            $sql = "INSERT INTO {$table} ({$keyField}, data) VALUES (?, ?)";
            return $this->executeUpdate($sql, [$id, json_encode($data)]);
        }
    }

    /**
     * Execute raw SQL query for complex JSONB operations
     *
     * @param string $sql    SQL query with placeholders
     * @param array  $params Parameters for the query
     *
     * @return array
     */
    public function rawQuery(string $sql, array $params = []): array
    {
        return $this->executeQuery($sql, $params);
    }
}
