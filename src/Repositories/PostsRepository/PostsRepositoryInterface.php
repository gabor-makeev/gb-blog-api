<?php

namespace Gabormakeev\GbBlogApi\Repositories\PostsRepository;

use Gabormakeev\GbBlogApi\Post;
use Gabormakeev\GbBlogApi\UUID;

interface PostsRepositoryInterface
{
    public function get(UUID $uuid): Post;

    public function save(Post $post): void;
}
