<?php

use Gabormakeev\GbBlogApi\Container\DIContainer;
use Gabormakeev\GbBlogApi\Repositories\CommentsRepository\CommentsRepositoryInterface;
use Gabormakeev\GbBlogApi\Repositories\CommentsRepository\SqliteCommentsRepository;
use Gabormakeev\GbBlogApi\Repositories\PostsRepository\PostsRepositoryInterface;
use Gabormakeev\GbBlogApi\Repositories\PostsRepository\SqlitePostsRepository;
use Gabormakeev\GbBlogApi\Repositories\UsersRepository\SqliteUsersRepository;
use Gabormakeev\GbBlogApi\Repositories\UsersRepository\UsersRepositoryInterface;

require_once __DIR__ . '/vendor/autoload.php';

$container = new DIContainer();

$container->bind(
    PDO::class,
    new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
);

$container->bind(
    PostsRepositoryInterface::class,
    SqlitePostsRepository::class
);

$container->bind(
    UsersRepositoryInterface::class,
    SqliteUsersRepository::class
);

$container->bind(
    CommentsRepositoryInterface::class,
    SqliteCommentsRepository::class
);

return $container;
