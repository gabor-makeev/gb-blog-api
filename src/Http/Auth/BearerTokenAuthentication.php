<?php

namespace Gabormakeev\GbBlogApi\Http\Auth;

use DateTimeImmutable;
use Gabormakeev\GbBlogApi\AuthToken;
use Gabormakeev\GbBlogApi\Exceptions\AuthException;
use Gabormakeev\GbBlogApi\Exceptions\AuthTokenNotFoundException;
use Gabormakeev\GbBlogApi\Exceptions\HttpException;
use Gabormakeev\GbBlogApi\Http\Request;
use Gabormakeev\GbBlogApi\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use Gabormakeev\GbBlogApi\Repositories\UsersRepository\UsersRepositoryInterface;
use Gabormakeev\GbBlogApi\User;

class BearerTokenAuthentication implements TokenAuthenticationInterface
{
    private const HEADER_PREFIX = 'Bearer ';

    public function __construct(
        private AuthTokensRepositoryInterface $authTokensRepository,
        private UsersRepositoryInterface $usersRepository,
    ) {}

    public function user(Request $request): User
    {
        $authToken = $this->getAuthToken($request);

        $userUuid = $authToken->getUserUuid();

        return $this->usersRepository->get($userUuid);
    }

    public function logout(Request $request): void
    {
        $authToken = $this->getAuthToken($request);

        $disabledAuthToken = new AuthToken(
            $authToken->getToken(),
            $authToken->getUserUuid(),
            new DateTimeImmutable()
        );

        $this->authTokensRepository->save($disabledAuthToken);
    }

    private function getAuthToken(Request $request): AuthToken
    {
        try {
            $header = $request->header('Authorization');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }

        if (!str_starts_with($header, self::HEADER_PREFIX)) {
            throw new AuthException("Malformed token: [$header]");
        }

        $token = mb_substr($header, strlen(self::HEADER_PREFIX));

        try {
            $authToken = $this->authTokensRepository->get($token);
        } catch (AuthTokenNotFoundException) {
            throw new AuthException("Bad token: [$token]");
        }

        if ($authToken->getExpiresOn() <= new DateTimeImmutable()) {
            throw new AuthException("Token expired: [$token]");
        }

        return $authToken;
    }
}
