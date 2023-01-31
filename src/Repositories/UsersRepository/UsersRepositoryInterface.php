<?php

namespace Gabormakeev\GbBlogApi\Repositories\UsersRepository;

use Gabormakeev\GbBlogApi\User;
use Gabormakeev\GbBlogApi\UUID;

interface UsersRepositoryInterface
{
    public function save(User $user): void;

    public function get(UUID $uuid): User;

    public function getByUsername(string $username): User;
}
