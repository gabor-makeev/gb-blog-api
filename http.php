<?php

use Gabormakeev\GbBlogApi\Exceptions\AppException;
use Gabormakeev\GbBlogApi\Http\Actions\Comments\CreateComment;
use Gabormakeev\GbBlogApi\Http\Actions\PostLikes\CreatePostLike;
use Gabormakeev\GbBlogApi\Http\Actions\Posts\CreatePost;
use Gabormakeev\GbBlogApi\Http\Actions\Posts\DeletePost;
use Gabormakeev\GbBlogApi\Http\Actions\Users\FindByUsername;
use Gabormakeev\GbBlogApi\Http\ErrorResponse;
use Gabormakeev\GbBlogApi\Http\Request;
use Gabormakeev\GbBlogApi\Exceptions\HttpException;
use Psr\Log\LoggerInterface;

$container = require __DIR__ . '/bootstrap.php';

$request = new Request(
    $_GET,
    $_SERVER,
    file_get_contents('php://input')
);

$logger = $container->get(LoggerInterface::class);

try {
    $path = $request->path();
} catch (HttpException $e) {
    $logger->warning($e->getMessage());
    (new ErrorResponse)->send();
    return;
}

try {
    $method = $request->method();
} catch (HttpException $e) {
    $logger->warning($e->getMessage());
    (new ErrorResponse)->send();
    return;
}

$routes = [
    'GET' => [
        '/users/show' => FindByUsername::class,
    // TODO:
    //    '/posts/show' => new FindByUuid(
    //        new SqlitePostsRepository(
    //            new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
    //        )
    //    )
    ],
    'POST' => [
        '/posts/create' => CreatePost::class,
        '/posts/comment' => CreateComment::class,
        '/postLikes/create' => CreatePostLike::class
    ],
    'DELETE' => [
        '/posts' => DeletePost::class
    ]
];

if (!array_key_exists($method, $routes)
   || !array_key_exists($path, $routes[$method])) {
    $message = "Route not found: $method $path";
    $logger->notice($message);
    (new ErrorResponse($message))->send();
    return;
}

$actionClassName = $routes[$method][$path];

try {
    $action = $container->get($actionClassName);
    $response = $action->handle($request);
} catch (AppException $e) {
    $logger->error($e->getMessage(), ['exception' => $e]);
    (new ErrorResponse)->send();
    return;
}

$response->send();
