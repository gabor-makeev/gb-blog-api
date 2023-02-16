<?php

namespace Gabormakeev\GbBlogApi\UnitTests\Commands;

use Gabormakeev\GbBlogApi\Commands\Arguments;
use Gabormakeev\GbBlogApi\Commands\CreateUserCommand;
use Gabormakeev\GbBlogApi\Exceptions\ArgumentsException;
use Gabormakeev\GbBlogApi\Exceptions\UserNotFoundException;
use Gabormakeev\GbBlogApi\Repositories\UsersRepository\DummyUsersRepository;
use Gabormakeev\GbBlogApi\Repositories\UsersRepository\UsersRepositoryInterface;
use Gabormakeev\GbBlogApi\UnitTests\DummyLogger;
use Gabormakeev\GbBlogApi\User;
use Gabormakeev\GbBlogApi\UUID;
use PHPUnit\Framework\TestCase;

class CreateUserCommandTest extends TestCase
{
    public function testItLogsAWarningWhenUserAlreadyExists(): void
    {
        $command = new CreateUserCommand(
            new DummyUsersRepository(),
            new DummyLogger()
        );

        $this->expectOutputString('User already exists: Ivan');

        $command->handle(new Arguments(['username' => 'Ivan', 'password' => 'test_password']));
    }

    public function testItSavesUserToRepository(): void
    {
        $usersRepository = new class implements UsersRepositoryInterface {
            private bool $called = false;

            public function save(User $user): void
            {
                $this->called = true;
            }

            public function get(UUID $uuid): User
            {
                throw new UserNotFoundException("Not found");
            }

            public function getByUsername(string $username): User
            {
                throw new UserNotFoundException("Not found");
            }

            public function wasCalled(): bool
            {
                return $this->called;
            }
        };

        $command = new CreateUserCommand($usersRepository, new DummyLogger());

        $command->handle(new Arguments([
            'username' => 'Ivan',
            'password' => 'some_password',
            'first_name' => 'Ivan',
            'last_name' => 'Nikitin'
        ]));

        $this->assertTrue($usersRepository->wasCalled());
    }

    public function testItRequiresFirstName(): void
    {
        $command = new CreateUserCommand($this->makeUsersRepository(), new DummyLogger());

        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: first_name');

        $command->handle(new Arguments(['username' => 'Ivan', 'password' => 'some_password']));
    }

    public function testItRequiresLastName(): void
    {
        $command = new CreateUserCommand($this->makeUsersRepository(), new DummyLogger());

        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: last_name');

        $command->handle(new Arguments([
            'username' => 'Ivan',
            'password' => 'some_password',
            'first_name' => 'Ivan'
        ]));
    }

    public function testItRequiresPassword(): void
    {
        $command = new CreateUserCommand($this->makeUsersRepository(), new DummyLogger());

        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: password');

        $command->handle(new Arguments([
            'username' => 'Ivan',
        ]));
    }

    private function makeUsersRepository(): UsersRepositoryInterface
    {
        return new class implements UsersRepositoryInterface {

            public function save(User $user): void
            {}

            public function get(UUID $uuid): User
            {
                throw new UserNotFoundException("Not found");
            }

            public function getByUsername(string $username): User
            {
                throw new UserNotFoundException("Not found");
            }
        };
    }

}
