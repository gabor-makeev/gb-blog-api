<?php

namespace Gabormakeev\GbBlogApi\Repositories\UsersRepository;

use Gabormakeev\GbBlogApi\Exceptions\UserNotFoundException;
use Gabormakeev\GbBlogApi\User;
use Gabormakeev\GbBlogApi\UUID;
use PDO;
use PDOStatement;
use Psr\Log\LoggerInterface;

class SqliteUsersRepository implements UsersRepositoryInterface
{
    public function __construct(
        private PDO $connection,
        private LoggerInterface $logger
    ) {}

    public function save(User $user): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO users (uuid, username, password, first_name, last_name) VALUES (:uuid, :username, :password, :first_name, :last_name)'
        );

        $statement->execute([
            ':uuid' => (string)$user->getUuid(),
            ':username' => $user->getUsername(),
            ':password' => $user->getHashedPassword(),
            ':first_name' => $user->getFirstName(),
            ':last_name' => $user->getLastName()
        ]);

        $this->logger->info("User saved: {$user->getUuid()}");
    }

    public function get(UUID $uuid): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE uuid = :uuid'
        );

        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);

        return $this->getUser($statement, $uuid);
    }

    public function getByUsername(string $username): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE username = :username'
        );

        $statement->execute([
            ':username' => $username
        ]);

        return $this->getUser($statement, $username);
    }

    private function getUser(PDOStatement $statement, string $username): User
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
            $this->logger->warning("Cannot find user: $username");
            throw new UserNotFoundException(
                "Cannot find user: $username"
            );
        }

        return new User(
            new UUID($result['uuid']),
            $result['username'],
            $result['password'],
            $result['first_name'],
            $result['last_name']
        );
    }
}
