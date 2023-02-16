<?php

namespace Gabormakeev\GbBlogApi\UnitTests\Actions;

use Gabormakeev\GbBlogApi\Exceptions\UserNotFoundException;
use Gabormakeev\GbBlogApi\Http\Actions\Users\FindByUsername;
use Gabormakeev\GbBlogApi\Http\ErrorResponse;
use Gabormakeev\GbBlogApi\Http\Request;
use Gabormakeev\GbBlogApi\Http\SuccessfulResponse;
use Gabormakeev\GbBlogApi\Repositories\UsersRepository\UsersRepositoryInterface;
use Gabormakeev\GbBlogApi\User;
use Gabormakeev\GbBlogApi\UUID;
use PHPUnit\Framework\TestCase;

class FindByUsernameActionTest extends TestCase
{
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testItReturnsErrorResponseIfNoUsernameProvided(): void
    {
        $request = new Request([], [], '');

        $usersRepository = $this->usersRepository([]);

        $action = new FindByUsername($usersRepository);

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);

        $this->expectOutputString('{"success":false,"reason":"No such query param in the request: username"}');

        $response->send();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testItReturnsErrorResponseIfUserNotFound(): void
    {
        $request = new Request(['username' => 'Ivan'], [], '');

        $usersRepository = $this->usersRepository([]);

        $action = new FindByUsername($usersRepository);

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);

        $this->expectOutputString('{"success":false,"reason":"Not found"}');

        $response->send();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testItReturnsSuccessfulResponse(): void
    {
        $request = new Request(['username' => 'ivan'], [], '');

        $usersRepository = $this->usersRepository([
            new User(
                UUID::random(),
                'ivan',
                'test password',
                'Ivan',
                'Nikitin'
            )
        ]);

        $action = new FindByUsername($usersRepository);

        $response = $action->handle($request);

        $this->assertInstanceOf(SuccessfulResponse::class, $response);

        $this->expectOutputString('{"success":true,"data":{"username":"ivan","name":"Ivan Nikitin"}}');

        $response->send();
    }

    private function usersRepository(array $users): UsersRepositoryInterface
    {
        return new class($users) implements UsersRepositoryInterface {
            public function __construct(
                private array $users
            ) {}

            public function save(User $user): void {}

            public function get(UUID $uuid): User
            {
                throw new UserNotFoundException("Not found");
            }

            public function getByUsername(string $username): User
            {
                foreach ($this->users as $user) {
                    if ($user instanceof User && $user->getUsername() === $username) {
                        return $user;
                    }
                }
                throw new UserNotFoundException("Not found");
            }
        };
    }
}
