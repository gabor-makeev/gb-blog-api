<?php

namespace Gabormakeev\GbBlogApi\Http\Actions\Auth;

use Gabormakeev\GbBlogApi\Exceptions\AuthException;
use Gabormakeev\GbBlogApi\Http\Actions\ActionInterface;
use Gabormakeev\GbBlogApi\Http\Auth\TokenAuthenticationInterface;
use Gabormakeev\GbBlogApi\Http\ErrorResponse;
use Gabormakeev\GbBlogApi\Http\Request;
use Gabormakeev\GbBlogApi\Http\Response;
use Gabormakeev\GbBlogApi\Http\SuccessfulResponse;

class LogOut implements ActionInterface
{
    public function __construct(
        private TokenAuthenticationInterface $authentication
    ) {}

    public function handle(Request $request): Response
    {
        try {
            $this->authentication->logout($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessfulResponse();
    }
}
