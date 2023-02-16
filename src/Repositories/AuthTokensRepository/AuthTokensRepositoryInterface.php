<?php

namespace Gabormakeev\GbBlogApi\Repositories\AuthTokensRepository;

use Gabormakeev\GbBlogApi\AuthToken;

interface AuthTokensRepositoryInterface
{
    public function save(AuthToken $authToken): void;

    public function get(string $token): AuthToken;
}
