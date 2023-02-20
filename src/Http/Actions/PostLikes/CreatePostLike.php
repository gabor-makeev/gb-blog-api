<?php

namespace Gabormakeev\GbBlogApi\Http\Actions\PostLikes;

use Gabormakeev\GbBlogApi\Exceptions\AuthException;
use Gabormakeev\GbBlogApi\Exceptions\HttpException;
use Gabormakeev\GbBlogApi\Exceptions\InvalidArgumentException;
use Gabormakeev\GbBlogApi\Exceptions\PostNotFoundException;
use Gabormakeev\GbBlogApi\Http\Actions\ActionInterface;
use Gabormakeev\GbBlogApi\Http\Auth\TokenAuthenticationInterface;
use Gabormakeev\GbBlogApi\Http\ErrorResponse;
use Gabormakeev\GbBlogApi\Http\Request;
use Gabormakeev\GbBlogApi\Http\Response;
use Gabormakeev\GbBlogApi\Http\SuccessfulResponse;
use Gabormakeev\GbBlogApi\PostLike;
use Gabormakeev\GbBlogApi\Repositories\PostLikesRepository\PostLikesRepositoryInterface;
use Gabormakeev\GbBlogApi\Repositories\PostsRepository\PostsRepositoryInterface;
use Gabormakeev\GbBlogApi\UUID;

class CreatePostLike implements ActionInterface
{
    public function __construct(
        private PostLikesRepositoryInterface $postLikesRepository,
        private PostsRepositoryInterface $postsRepository,
        private TokenAuthenticationInterface $authentication
    ) {}

    public function handle(Request $request): Response
    {
        try {
            $user = $this->authentication->user($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $postUuid = new UUID($request->jsonBodyField('post_uuid'));
        } catch (HttpException | InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $this->postsRepository->get($postUuid);
        } catch (PostNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $newPostLikeUuid = UUID::random();

        try {
            $postLike = new PostLike(
                $newPostLikeUuid,
                $postUuid,
                $user->getUuid()
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        if ($this->postLikesRepository->findByPostUuidAndUserUuid($postUuid, $user->getUuid())) {
            return new ErrorResponse(
                'User cannot like post more than once'
            );
        }

        $this->postLikesRepository->save($postLike);

        return new SuccessfulResponse([
            'uuid' => (string)$newPostLikeUuid
        ]);
    }
}
