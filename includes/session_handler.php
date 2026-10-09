<?php

class PdoSessionHandler implements SessionHandlerInterface
{
    private PDO $pdo;

    private PDOStatement $readStatement;
    private PDOStatement $writeStatement;
    private PDOStatement $destroyStatement;
    private PDOStatement $gcStatement;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;

        $this->readStatement = $pdo->prepare(
            'SELECT data FROM user_sessions WHERE id = :id'
        );

        $this->writeStatement = $pdo->prepare(
            'INSERT INTO user_sessions (id, data, last_accessed)
             VALUES (:id, :data, CURRENT_TIMESTAMP)
             ON CONFLICT (id) DO UPDATE
             SET data = EXCLUDED.data,
                 last_accessed = CURRENT_TIMESTAMP'
        );

        $this->destroyStatement = $pdo->prepare(
            'DELETE FROM user_sessions WHERE id = :id'
        );

        $this->gcStatement = $pdo->prepare(
            'DELETE FROM user_sessions
             WHERE last_accessed < NOW() - (:max_lifetime * INTERVAL \'1 second\')'
        );
    }

    public function open(string $path, string $name): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read(string $id): string|false
    {
        $this->readStatement->execute(['id' => $id]);

        $row = $this->readStatement->fetch();

        $this->readStatement->closeCursor();

        return $row ? $row['data'] : '';
    }

    public function write(string $id, string $data): bool
    {
        return $this->writeStatement->execute([
            'id' => $id,
            'data' => $data,
        ]);
    }

    public function destroy(string $id): bool
    {
        return $this->destroyStatement->execute(['id' => $id]);
    }

    public function gc(int $max_lifetime): int|false
    {
        $this->gcStatement->execute([
            'max_lifetime' => $max_lifetime,
        ]);

        $deleted = $this->gcStatement->rowCount();
        $this->gcStatement->closeCursor();

        return $deleted;
    }
}