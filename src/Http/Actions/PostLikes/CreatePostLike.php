<?php

namespace Gabormakeev\GbBlogApi\Http\Actions\PostLikes;

use Gabormakeev\GbBlogApi\Exceptions\HttpException;
use Gabormakeev\GbBlogApi\Exceptions\InvalidArgumentException;
use Gabormakeev\GbBlogApi\Exceptions\PostNotFoundException;
use Gabormakeev\GbBlogApi\Exceptions\UserNotFoundException;
use Gabormakeev\GbBlogApi\Http\Actions\ActionInterface;
use Gabormakeev\GbBlogApi\Http\ErrorResponse;
use Gabormakeev\GbBlogApi\Http\Request;
use Gabormakeev\GbBlogApi\Http\Response;
use Gabormakeev\GbBlogApi\Http\SuccessfulResponse;
use Gabormakeev\GbBlogApi\PostLike;
use Gabormakeev\GbBlogApi\Repositories\PostLikesRepository\SqlitePostLikesRepository;
use Gabormakeev\GbBlogApi\Repositories\PostsRepository\SqlitePostsRepository;
use Gabormakeev\GbBlogApi\Repositories\UsersRepository\SqliteUsersRepository;
use Gabormakeev\GbBlogApi\UUID;

class CreatePostLike implements ActionInterface
{
    public function __construct(
        private SqlitePostLikesRepository $postLikesRepository,
        private SqlitePostsRepository $postsRepository,
        private SqliteUsersRepository $usersRepository
    ) {}

    public function handle(Request $request): Response
    {
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

        try {
            $userUuid = new UUID($request->jsonBodyField('user_uuid'));
        } catch (HttpException | InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $this->usersRepository->get($userUuid);
        } catch (UserNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $newPostLikeUuid = UUID::random();

        try {
            $postLike = new PostLike(
                $newPostLikeUuid,
                $postUuid,
                $userUuid
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        if ($this->postLikesRepository->findByPostUuidAndUserUuid($postUuid, $userUuid)) {
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
