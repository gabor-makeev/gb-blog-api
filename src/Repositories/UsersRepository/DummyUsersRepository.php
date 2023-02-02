<?php

namespace Gabormakeev\GbBlogApi\Repositories\UsersRepository;

use Gabormakeev\GbBlogApi\Exceptions\UserNotFoundException;
use Gabormakeev\GbBlogApi\User;
use Gabormakeev\GbBlogApi\UUID;

class DummyUsersRepository implements UsersRepositoryInterface
{

    public function save(User $user): void
    {}

    public function get(UUID $uuid): User
    {
        throw new UserNotFoundException("Not found");
    }

    public function getByUsername(string $username): User
    {
        return new User(
            UUID::random(),
            "user123",
            "first",
            "last"
        );
    }
}
