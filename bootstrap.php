<?php

use Dotenv\Dotenv;
use Gabormakeev\GbBlogApi\Container\DIContainer;
use Gabormakeev\GbBlogApi\Http\Auth\IdentificationInterface;
use Gabormakeev\GbBlogApi\Http\Auth\JsonBodyUuidIdentification;
use Gabormakeev\GbBlogApi\Repositories\CommentsRepository\CommentsRepositoryInterface;
use Gabormakeev\GbBlogApi\Repositories\CommentsRepository\SqliteCommentsRepository;
use Gabormakeev\GbBlogApi\Repositories\PostLikesRepository\PostLikesRepositoryInterface;
use Gabormakeev\GbBlogApi\Repositories\PostLikesRepository\SqlitePostLikesRepository;
use Gabormakeev\GbBlogApi\Repositories\PostsRepository\PostsRepositoryInterface;
use Gabormakeev\GbBlogApi\Repositories\PostsRepository\SqlitePostsRepository;
use Gabormakeev\GbBlogApi\Repositories\UsersRepository\SqliteUsersRepository;
use Gabormakeev\GbBlogApi\Repositories\UsersRepository\UsersRepositoryInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

require_once __DIR__ . '/vendor/autoload.php';

Dotenv::createImmutable(__DIR__)->safeLoad();

$container = new DIContainer();

$logger = (new Logger('blog'));

if ($_SERVER['LOG_TO_FILES'] === 'yes') {
    $logger
        ->pushHandler(new StreamHandler(
            __DIR__ . '/logs/blog.log'
        ))
        ->pushHandler(new StreamHandler(
            __DIR__ . '/logs/blog.error.log',
            level: Logger::ERROR,
            bubble: false,
        ));
}

if ($_SERVER['LOG_TO_CONSOLE'] === 'yes') {
    $logger
        ->pushHandler(
            new StreamHandler("php://stdout")
        );
}

$container->bind(
    PDO::class,
    new PDO('sqlite:' . __DIR__ . '/' . $_SERVER['SQLITE_DB_PATH'])
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

$container->bind(
    PostLikesRepositoryInterface::class,
    SqlitePostLikesRepository::class
);

$container->bind(
    LoggerInterface::class,
    $logger
);

$container->bind(
    IdentificationInterface::class,
    JsonBodyUuidIdentification::class
);

return $container;
