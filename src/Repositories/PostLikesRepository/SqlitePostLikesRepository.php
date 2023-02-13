<?php

namespace Gabormakeev\GbBlogApi\Repositories\PostLikesRepository;

use Gabormakeev\GbBlogApi\PostLike;
use Gabormakeev\GbBlogApi\UUID;
use PDO;
use Psr\Log\LoggerInterface;

class SqlitePostLikesRepository implements PostLikesRepositoryInterface
{
    public function __construct(
        private PDO $connection,
        private LoggerInterface $logger
    ) {}

    public function save(PostLike $like): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO post_likes (uuid, post_uuid, user_uuid) VALUES (:uuid, :post_uuid, :user_uuid)'
        );

        $statement->execute([
            ':uuid' => (string)$like->getUuid(),
            ':post_uuid' => (string)$like->getPostUuid(),
            ':user_uuid' => (string)$like->getUserUuid()
        ]);

        $this->logger->info("PostLike saved: {$like->getUuid()}");
    }

    public function getByPostUuid(UUID $postUuid): array
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM post_likes WHERE post_uuid = :post_uuid'
        );

        $statement->execute([
            ':post_uuid' => (string)$postUuid
        ]);

        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (empty($result)) {
            $this->logger->warning("Cannot find PostLikes for post: $postUuid");
        }

        $postLikes = [];

        foreach ($result as $postLike) {
            $postLikes[] = new PostLike(
                new UUID($postLike['uuid']),
                new UUID($postLike['post_uuid']),
                new UUID($postLike['user_uuid'])
            );
        }

        return $postLikes;
    }

    public function findByPostUuidAndUserUuid(UUID $postUuid, UUID $userUuid): ?PostLike
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM post_likes WHERE post_uuid = :post_uuid AND user_uuid = :user_uuid'
        );

        $statement->execute([
            ':post_uuid' => (string)$postUuid,
            ':user_uuid' => (string)$userUuid
        ]);

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
            return null;
        }

        return new PostLike(
            new UUID($result['uuid']),
            new UUID($result['post_uuid']),
            new UUID($result['user_uuid'])
        );
    }
}
