<?php

namespace Gabormakeev\GbBlogApi\UnitTests\Repositories\UsersRepository;

use Gabormakeev\GbBlogApi\Exceptions\UserNotFoundException;
use Gabormakeev\GbBlogApi\Repositories\UsersRepository\SqliteUsersRepository;
use Gabormakeev\GbBlogApi\UnitTests\DummyLogger;
use Gabormakeev\GbBlogApi\User;
use Gabormakeev\GbBlogApi\UUID;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;

class SqliteUsersRepositoryTest extends TestCase
{
    public function testItThrowsAnExceptionWhenUserNotFound(): void
    {
        $connectionStub = $this->createStub(PDO::class);

        $statementStub = $this->createStub(PDOStatement::class);

        $statementStub->method('fetch')->willReturn(false);

        $connectionStub->method('prepare')->willReturn($statementStub);

        $repository = new SqliteUsersRepository($connectionStub, new DummyLogger());

        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('Cannot find user: Ivan');
        $this->expectOutputString('Cannot find user: Ivan');

        $repository->getByUsername('Ivan');
    }

    public function testItSavesUserToDatabase(): void
    {
        $connectionStub = $this->createStub(PDO::class);

        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':uuid' => '123e4567-e89b-12d3-a456-426614174000',
                ':username' => 'ivan123',
                ':first_name' => 'Ivan',
                ':last_name' => 'Nikitin',
            ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $repository = new SqliteUsersRepository($connectionStub, new DummyLogger());

        $repository->save(
            new User(
                new UUID('123e4567-e89b-12d3-a456-426614174000'),
                'ivan123',
                'Ivan',
                'Nikitin'
            )
        );
    }
}
