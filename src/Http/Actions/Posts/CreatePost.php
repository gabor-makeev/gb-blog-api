<?php

namespace Gabormakeev\GbBlogApi\Http\Actions\Posts;

use Gabormakeev\GbBlogApi\Exceptions\AuthException;
use Gabormakeev\GbBlogApi\Exceptions\HttpException;
use Gabormakeev\GbBlogApi\Http\Actions\ActionInterface;
use Gabormakeev\GbBlogApi\Http\Auth\TokenAuthenticationInterface;
use Gabormakeev\GbBlogApi\Http\ErrorResponse;
use Gabormakeev\GbBlogApi\Http\Request;
use Gabormakeev\GbBlogApi\Http\Response;
use Gabormakeev\GbBlogApi\Http\SuccessfulResponse;
use Gabormakeev\GbBlogApi\Post;
use Gabormakeev\GbBlogApi\Repositories\PostsRepository\PostsRepositoryInterface;
use Gabormakeev\GbBlogApi\UUID;
use Psr\Log\LoggerInterface;

class CreatePost implements ActionInterface
{
    public function __construct(
        private PostsRepositoryInterface $postsRepository,
        private TokenAuthenticationInterface $authentication,
        private LoggerInterface $logger,
    ) {}

    public function handle(Request $request): Response
    {
        try {
            $author = $this->authentication->user($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $newPostUuid = UUID::random();

        try {
            $post = new Post(
                $newPostUuid,
                $author->getUuid(),
                $request->jsonBodyField('title'),
                $request->jsonBodyField('text'),
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $this->postsRepository->save($post);

        $this->logger->info("Post created: $newPostUuid");

        return new SuccessfulResponse([
            'uuid' => (string)$newPostUuid
        ]);
    }
}
