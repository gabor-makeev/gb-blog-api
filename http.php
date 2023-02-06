<?php

use Gabormakeev\GbBlogApi\Exceptions\AppException;
use Gabormakeev\GbBlogApi\Http\Actions\Posts\CreatePost;
use Gabormakeev\GbBlogApi\Http\Actions\Users\FindByUsername;
use Gabormakeev\GbBlogApi\Http\ErrorResponse;
use Gabormakeev\GbBlogApi\Http\Request;
use Gabormakeev\GbBlogApi\Exceptions\HttpException;
use Gabormakeev\GbBlogApi\Repositories\PostsRepository\SqlitePostsRepository;
use Gabormakeev\GbBlogApi\Repositories\UsersRepository\SqliteUsersRepository;

require_once __DIR__ . '/vendor/autoload.php';

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
        '/users/show' => new FindByUsername(
            new SqliteUsersRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        ),
    // TODO:
    //    '/posts/show' => new FindByUuid(
    //        new SqlitePostsRepository(
    //            new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
    //        )
    //    )
    ],
    'POST' => [
        '/posts/create' => new CreatePost(
            new SqlitePostsRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            ),
            new SqliteUsersRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        )
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

$action = $routes[$method][$path];

try {
    $response = $action->handle($request);
} catch (AppException $e) {
    (new ErrorResponse($e->getMessage()))->send();
}

$response->send();
