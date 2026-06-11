<?php
class CacheModel {
    private PDO $db;
    private int $ttl = 3600;
    private string $table;

    public function __construct() {
        $this->db = Database::connect();
        $this->table = DB_TABLE_CACHE;
    }

    public function get(string $key): ?array {
        $stmt = $this->db->prepare(
            "SELECT data, created_at FROM {$this->table} WHERE cache_key = ?"
        );
        $stmt->execute([$key]);
        $row = $stmt->fetch();

        if (!$row) return null;

        $age = time() - strtotime($row['created_at']);
        if ($age > $this->ttl) {
            $this->delete($key);
            return null;
        }

        return json_decode($row['data'], true);
    }

    public function set(string $key, array $data): void {
        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} (cache_key, data)
             VALUES (?, ?)
             ON DUPLICATE KEY UPDATE data = VALUES(data), created_at = NOW()"
        );
        $stmt->execute([$key, json_encode($data)]);
    }

    public function delete(string $key): void {
        $stmt = $this->db->prepare(
            "DELETE FROM {$this->table} WHERE cache_key = ?"
        );
        $stmt->execute([$key]);
    }
}