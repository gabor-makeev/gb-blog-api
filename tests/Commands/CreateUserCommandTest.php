<?php

namespace Gabormakeev\GbBlogApi\UnitTests\Commands;

use Gabormakeev\GbBlogApi\Commands\Users\CreateUser;
use Gabormakeev\GbBlogApi\Exceptions\UserNotFoundException;
use Gabormakeev\GbBlogApi\Repositories\UsersRepository\DummyUsersRepository;
use Gabormakeev\GbBlogApi\Repositories\UsersRepository\UsersRepositoryInterface;
use Gabormakeev\GbBlogApi\User;
use Gabormakeev\GbBlogApi\UUID;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Tester\CommandTester;

class CreateUserCommandTest extends TestCase
{
    public function testItLogsAWarningWhenUserAlreadyExists(): void
    {
        $command = new CreateUser(
            new DummyUsersRepository(),
        );

        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'username' => 'Ivan',
            'password' => 'some_password',
            'first_name' => 'Ivan',
            'last_name' => 'Nikitin'
        ]);

        $expected = 'Create user command started' . PHP_EOL . "User already exists: Ivan\n";

        $output = $commandTester->getDisplay();

        $this->assertSame($expected, $output);
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

        $command = new CreateUser($usersRepository);

        $command->run(
            new ArrayInput([
                'username' => 'Ivan',
                'password' => 'some_password',
                'first_name' => 'Ivan',
                'last_name' => 'Nikitin'
            ]),
            new NullOutput()
        );

        $this->assertTrue($usersRepository->wasCalled());
    }

    public function testItRequiresFirstName(): void
    {
        $command = new CreateUser($this->makeUsersRepository());

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments (missing: "first_name").');

        $command->run(
            new ArrayInput([
                'username' => 'Ivan',
                'password' => 'some_password',
                'last_name' => 'Nikitin'
            ]),
            new NullOutput()
        );
    }

    public function testItRequiresLastName(): void
    {
        $command = new CreateUser($this->makeUsersRepository());

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments (missing: "last_name").');

        $command->run(
            new ArrayInput([
                'username' => 'Ivan',
                'password' => 'some_password',
                'first_name' => 'Ivan'
            ]),
            new NullOutput()
        );
    }

    public function testItRequiresPassword(): void
    {
        $command = new CreateUser($this->makeUsersRepository());

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments (missing: "password").');

        $command->run(
            new ArrayInput([
                'username' => 'Ivan',
                'first_name' => 'Ivan',
                'last_name' => 'Nikitin'
            ]),
            new NullOutput()
        );
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
