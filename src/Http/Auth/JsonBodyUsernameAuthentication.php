<?php

namespace Gabormakeev\GbBlogApi\Http\Auth;

use Gabormakeev\GbBlogApi\Exceptions\AuthException;
use Gabormakeev\GbBlogApi\Exceptions\HttpException;
use Gabormakeev\GbBlogApi\Exceptions\UserNotFoundException;
use Gabormakeev\GbBlogApi\Http\Request;
use Gabormakeev\GbBlogApi\Repositories\UsersRepository\UsersRepositoryInterface;
use Gabormakeev\GbBlogApi\User;

class JsonBodyUsernameAuthentication implements AuthenticationInterface
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    ) {}

    public function user(Request $request): User
    {
        try {
            $username = $request->jsonBodyField('username');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }

        try {
            return $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException $e) {
            throw new AuthException($e->getMessage());
        }
    }
}
