<?php

namespace Gabormakeev\GbBlogApi\Repositories\PostLikesRepository;

use Gabormakeev\GbBlogApi\PostLike;
use Gabormakeev\GbBlogApi\UUID;

interface PostLikesRepositoryInterface
{
    public function save(PostLike $like): void;

    public function getByPostUuid(UUID $postUuid): array;
}
