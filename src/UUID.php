<?php

namespace Gabormakeev\GbBlogApi;

use Gabormakeev\GbBlogApi\Exceptions\InvalidArgumentException;

class UUID
{
    public function __construct(
        private string $uuidString
    ) {
        if (!uuid_is_valid($uuidString)) {
            throw new InvalidArgumentException(
                "Malformed UUID: $this->uuidString"
            );
        }
    }

    public static function random(): self
    {
        return new self(uuid_create(UUID_TYPE_RANDOM));
    }

    public function __toString(): string
    {
        return $this->uuidString;
    }
}
