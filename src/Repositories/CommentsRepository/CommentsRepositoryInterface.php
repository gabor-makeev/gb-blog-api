<?php

namespace Gabormakeev\GbBlogApi\Repositories\CommentsRepository;

use Gabormakeev\GbBlogApi\Comment;
use Gabormakeev\GbBlogApi\UUID;

interface CommentsRepositoryInterface
{
    public function get(UUID $uuid): Comment;

    public function save(Comment $comment): void;
}
