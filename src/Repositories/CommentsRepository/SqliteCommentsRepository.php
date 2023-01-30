<?php

namespace Gabormakeev\GbBlogApi\Repositories\CommentsRepository;

use Gabormakeev\GbBlogApi\Comment;
use Gabormakeev\GbBlogApi\Exceptions\CommentNotFoundException;
use Gabormakeev\GbBlogApi\UUID;
use PDO;
use PDOStatement;

class SqliteCommentsRepository implements CommentsRepositoryInterface
{
    public function __construct(
        private PDO $connection
    ) {}

    public function save(Comment $comment): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO comments (uuid, post_uuid, author_uuid, text) VALUES (:uuid, :post_uuid, :author_uuid, :text)'
        );

        $statement->execute([
            ':uuid' => (string)$comment->getUuid(),
            ':post_uuid' => (string)$comment->getPostUuid(),
            ':author_uuid' => (string)$comment->getAuthorUuid(),
            ':text' => $comment->getText()
        ]);
    }

    public function get(UUID $uuid): Comment
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM comments WHERE uuid = :uuid'
        );

        $statement->execute([
            ':uuid' => (string)$uuid
        ]);

        return $this->getComment($statement, $uuid);
    }

    private function getComment(PDOStatement $statement, UUID $uuid): Comment
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
            throw new CommentNotFoundException(
                "Cannot find comment: $uuid"
            );
        }

        return new Comment(
            new UUID($result['uuid']),
            new UUID($result['author_uuid']),
            new UUID($result['post_uuid']),
            $result['text']
        );
    }
}
