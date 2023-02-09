<?php

namespace Gabormakeev\GbBlogApi\Repositories\PostLikesRepository;

use Gabormakeev\GbBlogApi\PostLike;
use Gabormakeev\GbBlogApi\UUID;
use PDO;

class SqlitePostLikesRepository implements PostLikesRepositoryInterface
{
    public function __construct(
        private PDO $connection
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
}
