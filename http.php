<?php

use Gabormakeev\GbBlogApi\Exceptions\AppException;
use Gabormakeev\GbBlogApi\Http\Actions\Comments\CreateComment;
use Gabormakeev\GbBlogApi\Http\Actions\Posts\CreatePost;
use Gabormakeev\GbBlogApi\Http\Actions\Posts\DeletePost;
use Gabormakeev\GbBlogApi\Http\Actions\Users\FindByUsername;
use Gabormakeev\GbBlogApi\Http\ErrorResponse;
use Gabormakeev\GbBlogApi\Http\Request;
use Gabormakeev\GbBlogApi\Exceptions\HttpException;

$container = require __DIR__ . '/bootstrap.php';

$request = new Request(
    $_GET,
    $_SERVER,
    file_get_contents('php://input')
);

try {
    $path = $request->path();
} catch (HttpException) {
    (new ErrorResponse)->send();
    return;
}

try {
    $method = $request->method();
} catch (HttpException) {
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
        '/posts/comment' => CreateComment::class
    ],
    'DELETE' => [
        '/posts' => DeletePost::class
    ]
];

if (!array_key_exists($method, $routes)) {
    (new ErrorResponse('Not found'))->send();
    return;
}

if (!array_key_exists($path, $routes[$method])) {
    (new ErrorResponse('Not found'))->send();
    return;
}

$actionClassName = $routes[$method][$path];

$action = $container->get($actionClassName);

try {
    $response = $action->handle($request);
} catch (AppException $e) {
    (new ErrorResponse($e->getMessage()))->send();
}

$response->send();
