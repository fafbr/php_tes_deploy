<?php
class PdoSessionHandler implements SessionHandlerInterface {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function open($savePath, $sessionName): bool { 
        return true; 
    }

    public function close(): bool { 
        return true; 
    }

    public function read($id): string|false {
        $stmt = $this->pdo->prepare("SELECT data FROM user_sessions WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? $row['data'] : '';
    }

    public function write($id, $data): bool {
        $stmt = $this->pdo->prepare("
            INSERT INTO user_sessions (id, data, last_accessed) 
            VALUES (:id, :data, CURRENT_TIMESTAMP)
            ON CONFLICT (id) DO UPDATE 
            SET data = EXCLUDED.data, last_accessed = CURRENT_TIMESTAMP
        ");
        return $stmt->execute(['id' => $id, 'data' => $data]);
    }

    public function destroy($id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM user_sessions WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function gc($maxlifetime): int|false {
        $stmt = $this->pdo->prepare("DELETE FROM user_sessions WHERE last_accessed < NOW() - INTERVAL '1 day'");
        return $stmt->execute();
    }
}