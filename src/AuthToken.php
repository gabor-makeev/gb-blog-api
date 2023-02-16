<?php

namespace Gabormakeev\GbBlogApi;

use DateTimeImmutable;

class AuthToken
{
    public function __construct(
        private string $token,
        private UUID $userUuid,
        private DateTimeImmutable $expiresOn
    ) {}

    public function __toString(): string
    {
        return $this->token;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @return UUID
     */
    public function getUserUuid(): UUID
    {
        return $this->userUuid;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getExpiresOn(): DateTimeImmutable
    {
        return $this->expiresOn;
    }
}
