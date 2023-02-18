<?php

namespace Gabormakeev\GbBlogApi\Http\Auth;

use Gabormakeev\GbBlogApi\Exceptions\AuthException;
use Gabormakeev\GbBlogApi\Exceptions\HttpException;
use Gabormakeev\GbBlogApi\Exceptions\UserNotFoundException;
use Gabormakeev\GbBlogApi\Http\Request;
use Gabormakeev\GbBlogApi\Repositories\UsersRepository\UsersRepositoryInterface;
use Gabormakeev\GbBlogApi\User;

class PasswordAuthentication implements PasswordAuthenticationInterface
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
            $user = $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException $e) {
            throw new AuthException($e->getMessage());
        }

        try {
            $password = $request->jsonBodyField('password');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }

        if (!$user->checkPassword($password)) {
            throw new AuthException('Wrong password');
        }

        return $user;
    }
}
