<?php

namespace Gabormakeev\GbBlogApi\UnitTests\Repositories\CommentsRepository;

use Gabormakeev\GbBlogApi\Comment;
use Gabormakeev\GbBlogApi\Exceptions\CommentNotFoundException;
use Gabormakeev\GbBlogApi\Repositories\CommentsRepository\SqliteCommentsRepository;
use Gabormakeev\GbBlogApi\UnitTests\DummyLogger;
use Gabormakeev\GbBlogApi\UUID;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;

class SqliteCommentsRepositoryTest extends TestCase
{
    public function testItSavesCommentToDatabase(): void
    {
        $comment = new Comment(
            new UUID('7709b807-aaaa-bbbb-cccc-2f5f961b8767'),
            new UUID('24abd8c6-aaaa-bbbb-cccc-ba55c24a58a5'),
            new UUID('2c484869-aaaa-bbbb-cccc-40bc40458fb4'),
            'Some comment'
        );

        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':uuid' => (string)$comment->getUuid(),
                ':post_uuid' => (string)$comment->getPostUuid(),
                ':author_uuid' => (string)$comment->getAuthorUuid(),
                ':text' => $comment->getText()
            ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $repository = new SqliteCommentsRepository($connectionStub, new DummyLogger());

        $repository->save($comment);
    }

    public function testItGetsCommentByUuid(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementStub = $this->createStub(PDOStatement::class);

        $statementStub->method('fetch')->willReturn([
            'uuid' => '7709b807-aaaa-bbbb-cccc-2f5f961b8767',
            'post_uuid' => '2c484869-aaaa-bbbb-cccc-40bc40458fb4',
            'author_uuid' => '24abd8c6-aaaa-bbbb-cccc-ba55c24a58a5',
            'text' => 'Some comment'
        ]);
        $connectionStub->method('prepare')->willReturn($statementStub);

        $repository = new SqliteCommentsRepository($connectionStub, new DummyLogger());

        $comment = $repository->get(new UUID('7709b807-aaaa-bbbb-cccc-2f5f961b8767'));

        $this->assertSame('7709b807-aaaa-bbbb-cccc-2f5f961b8767', (string)$comment->getUuid());
    }

    public function testItThrowsAnExceptionWhenCommentNotFound(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementStub = $this->createStub(PDOStatement::class);

        $statementStub->method('fetch')->willReturn(false);
        $connectionStub->method('prepare')->willReturn($statementStub);

        $repository = new SqliteCommentsRepository($connectionStub, new DummyLogger());

        $this->expectException(CommentNotFoundException::class);
        $this->expectExceptionMessage('Cannot find comment: 7709b807-aaaa-bbbb-cccc-2f5f961b8767');
        $this->expectOutputString('Cannot find comment: 7709b807-aaaa-bbbb-cccc-2f5f961b8767');

        $repository->get(new UUID('7709b807-aaaa-bbbb-cccc-2f5f961b8767'));
    }
}
