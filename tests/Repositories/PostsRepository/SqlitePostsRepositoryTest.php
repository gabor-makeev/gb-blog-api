<?php

namespace Gabormakeev\GbBlogApi\UnitTests\Repositories\PostsRepository;

use Gabormakeev\GbBlogApi\Exceptions\PostNotFoundException;
use Gabormakeev\GbBlogApi\Post;
use Gabormakeev\GbBlogApi\Repositories\PostsRepository\SqlitePostsRepository;
use Gabormakeev\GbBlogApi\UnitTests\DummyLogger;
use Gabormakeev\GbBlogApi\UUID;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;

class SqlitePostsRepositoryTest extends TestCase
{
    public function testItSavesPostToDatabase(): void
    {
        $post = new Post(
            new UUID('1e401f4e-aaaa-bbbb-cccc-6753408fb660'),
            new UUID('ad6f62fb-dddd-eeee-ffff-1719e6964d52'),
            'Some title',
            'Some text'
        );

        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':uuid' => (string)$post->getUuid(),
                ':author_uuid' => (string)$post->getAuthorUuid(),
                ':title' => $post->getTitle(),
                ':text' => $post->getText()
            ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $repository = new SqlitePostsRepository($connectionStub, new DummyLogger());

        $repository->save($post);
    }

    public function testItGetsPostByUuid(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementStub = $this->createStub(PDOStatement::class);

        $statementStub->method('fetch')->willReturn([
            'uuid' => '1e401f4e-aaaa-bbbb-cccc-6753408fb660',
            'author_uuid' => 'ad6f62fb-dddd-eeee-ffff-1719e6964d52',
            'title' => 'Some title',
            'text' => 'Some text'
        ]);
        $connectionStub->method('prepare')->willReturn($statementStub);

        $repository = new SqlitePostsRepository($connectionStub, new DummyLogger());

        $post = $repository->get(new UUID('1e401f4e-aaaa-bbbb-cccc-6753408fb660'));

        $this->assertSame('1e401f4e-aaaa-bbbb-cccc-6753408fb660', (string)$post->getUuid());
    }

    public function testItThrowsAnExceptionWhenPostNotFound(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementStub = $this->createStub(PDOStatement::class);

        $statementStub->method('fetch')->willReturn(false);
        $connectionStub->method('prepare')->willReturn($statementStub);

        $repository = new SqlitePostsRepository($connectionStub, new DummyLogger());

        $this->expectException(PostNotFoundException::class);
        $this->expectExceptionMessage('Cannot find post: 1e401f4e-aaaa-bbbb-cccc-6753408fb660');
        $this->expectOutputString('Cannot find post: 1e401f4e-aaaa-bbbb-cccc-6753408fb660');

        $repository->get(new UUID('1e401f4e-aaaa-bbbb-cccc-6753408fb660'));
    }
}
