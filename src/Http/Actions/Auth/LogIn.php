<?php

namespace Gabormakeev\GbBlogApi\Http\Actions\Auth;

use DateTimeImmutable;
use Gabormakeev\GbBlogApi\AuthToken;
use Gabormakeev\GbBlogApi\Exceptions\AuthException;
use Gabormakeev\GbBlogApi\Http\Actions\ActionInterface;
use Gabormakeev\GbBlogApi\Http\Auth\PasswordAuthenticationInterface;
use Gabormakeev\GbBlogApi\Http\ErrorResponse;
use Gabormakeev\GbBlogApi\Http\Request;
use Gabormakeev\GbBlogApi\Http\Response;
use Gabormakeev\GbBlogApi\Http\SuccessfulResponse;
use Gabormakeev\GbBlogApi\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;

class LogIn implements ActionInterface
{
    public function __construct(
        private PasswordAuthenticationInterface $passwordAuthentication,
        private AuthTokensRepositoryInterface $authTokensRepository
    ) {}

    public function handle(Request $request): Response
    {
        try {
            $user = $this->passwordAuthentication->user($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $authToken = new AuthToken(
            bin2hex(random_bytes(40)),
            $user->getUuid(),
            (new DateTimeImmutable())->modify('+1 day')
        );

        $this->authTokensRepository->save($authToken);

        return new SuccessfulResponse([
            'token' => (string)$authToken,
        ]);
    }
}
