<?php

namespace Gabormakeev\GbBlogApi\UnitTests\Actions;

use Gabormakeev\GbBlogApi\Exceptions\HttpException;
use Gabormakeev\GbBlogApi\Exceptions\InvalidArgumentException;
use Gabormakeev\GbBlogApi\Exceptions\UserNotFoundException;
use Gabormakeev\GbBlogApi\Http\Actions\Posts\CreatePost;
use Gabormakeev\GbBlogApi\Http\Auth\TokenAuthenticationInterface;
use Gabormakeev\GbBlogApi\Http\Request;
use Gabormakeev\GbBlogApi\Http\SuccessfulResponse;
use Gabormakeev\GbBlogApi\Repositories\PostsRepository\PostsRepositoryInterface;
use Gabormakeev\GbBlogApi\UnitTests\DummyLogger;
use Gabormakeev\GbBlogApi\User;
use Gabormakeev\GbBlogApi\UUID;
use PHPUnit\Framework\TestCase;

class CreatePostActionTest extends TestCase
{
    public function testItReturnsSuccessfulResponse(): void
    {
        $request = new Request([], [], '{"author_uuid": "1235a110-ee17-4136-91ba-41f720a6d6b4","title": "some test post title","text": "some test post text"}');

        $postsRepository = $this->createStub(PostsRepositoryInterface::class);

        $authentication = $this->authentication([
            new User(
                new UUID('1235a110-ee17-4136-91ba-41f720a6d6b4'),
                'test username',
                'test password',
                'test firstname',
                'test lastname'
            )
        ]);

        $action = new CreatePost($postsRepository, $authentication, new DummyLogger());

        $response = $action->handle($request);

        $this->assertInstanceOf(SuccessfulResponse::class, $response);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testItReturnsExceptionIfMalformedUuid(): void
    {
        $request = new Request([], [], '{"author_uuid": "1235a110-ee17-4136-91ba-41f720a6d6b","title": "some test post title","text": "some test post text"}');

        $postsRepository = $this->createStub(PostsRepositoryInterface::class);

        $authentication = $this->authentication([]);

        $action = new CreatePost($postsRepository, $authentication, new DummyLogger());

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Malformed UUID: 1235a110-ee17-4136-91ba-41f720a6d6b');

        $action->handle($request);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testItReturnsExceptionIfUserNotFound(): void
    {
        $request = new Request([], [], '{"author_uuid": "1235a110-ee17-4136-91ba-41f720a6d6b4","title": "some test post title","text": "some test post text"}');

        $postsRepository = $this->createStub(PostsRepositoryInterface::class);

        $authentication = $this->authentication([]);

        $action = new CreatePost($postsRepository, $authentication, new DummyLogger());

        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('Cannot find user: 1235a110-ee17-4136-91ba-41f720a6d6b4');

        $action->handle($request);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testItReturnsExceptionIfMissingParametersInBody(): void
    {
        $request = new Request([], [], '{}');

        $postsRepository = $this->createStub(PostsRepositoryInterface::class);

        $authentication = $this->authentication([]);

        $action = new CreatePost($postsRepository, $authentication, new DummyLogger());

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('No such field: author_uuid');

        $action->handle($request);
    }

    private function authentication(array $users): TokenAuthenticationInterface
    {
        return new class($users) implements TokenAuthenticationInterface {
            public function __construct(
                private array $users
            ) {}

            public function user(Request $request): User
            {
                $authorUuid = new UUID($request->jsonBodyField('author_uuid'));
                foreach ($this->users as $user) {
                    if ($user instanceof User && (string)$user->getUuid() === (string)$authorUuid) {
                        return $user;
                    };
                }
                throw new UserNotFoundException("Cannot find user: $authorUuid");
            }
        };
    }
}
