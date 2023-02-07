<?php

namespace Gabormakeev\GbBlogApi\UnitTests\Actions;

use Gabormakeev\GbBlogApi\Exceptions\UserNotFoundException;
use Gabormakeev\GbBlogApi\Http\Actions\Posts\CreatePost;
use Gabormakeev\GbBlogApi\Http\ErrorResponse;
use Gabormakeev\GbBlogApi\Http\Request;
use Gabormakeev\GbBlogApi\Http\SuccessfulResponse;
use Gabormakeev\GbBlogApi\Repositories\PostsRepository\PostsRepositoryInterface;
use Gabormakeev\GbBlogApi\Repositories\UsersRepository\UsersRepositoryInterface;
use Gabormakeev\GbBlogApi\User;
use Gabormakeev\GbBlogApi\UUID;
use PHPUnit\Framework\TestCase;

class CreatePostActionTest extends TestCase
{
    public function testItReturnsSuccessfulResponse(): void
    {
        $request = new Request([], [], '{"author_uuid": "1235a110-ee17-4136-91ba-41f720a6d6b4","title": "some test post title","text": "some test post text"}');

        $postsRepository = $this->createStub(PostsRepositoryInterface::class);

        $usersRepository = $this->usersRepository([
            new User(
                new UUID('1235a110-ee17-4136-91ba-41f720a6d6b4'),
                'test username',
                'test firstname',
                'test lastname'
            )
        ]);

        $action = new CreatePost($postsRepository, $usersRepository);

        $response = $action->handle($request);

        $this->assertInstanceOf(SuccessfulResponse::class, $response);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testItReturnsErrorResponseIfMalformedUuid(): void
    {
        $request = new Request([], [], '{"author_uuid": "1235a110-ee17-4136-91ba-41f720a6d6b","title": "some test post title","text": "some test post text"}');

        $postsRepository = $this->createStub(PostsRepositoryInterface::class);

        $usersRepository = $this->usersRepository([]);

        $action = new CreatePost($postsRepository, $usersRepository);

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);

        $this->expectOutputString('{"success":false,"reason":"Malformed UUID: 1235a110-ee17-4136-91ba-41f720a6d6b"}');

        $response->send();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testItReturnsErrorResponseIfUserNotFound(): void
    {
        $request = new Request([], [], '{"author_uuid": "1235a110-ee17-4136-91ba-41f720a6d6b4","title": "some test post title","text": "some test post text"}');

        $postsRepository = $this->createStub(PostsRepositoryInterface::class);

        $usersRepository = $this->usersRepository([]);

        $action = new CreatePost($postsRepository, $usersRepository);

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);

        $this->expectOutputString('{"success":false,"reason":"Cannot find user: 1235a110-ee17-4136-91ba-41f720a6d6b4"}');

        $response->send();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testItReturnsErrorResponseIfMissingParametersInBody(): void
    {
        $request = new Request([], [], '{}');

        $postsRepository = $this->createStub(PostsRepositoryInterface::class);

        $usersRepository = $this->usersRepository([]);

        $action = new CreatePost($postsRepository, $usersRepository);

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);

        $this->expectOutputString('{"success":false,"reason":"No such field: author_uuid"}');

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
                foreach ($this->users as $user) {
                    if ($user instanceof User && (string)$user->getUuid() === (string)$uuid) {
                        return $user;
                    }
                }
                throw new UserNotFoundException("Cannot find user: $uuid");
            }

            public function getByUsername(string $username): User
            {
                throw new UserNotFoundException("Not found");
            }
        };
    }
}
