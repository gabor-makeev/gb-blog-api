<?php

use Gabormakeev\GbBlogApi\Exceptions\AppException;
use Gabormakeev\GbBlogApi\Http\Actions\Users\FindByUsername;
use Gabormakeev\GbBlogApi\Http\ErrorResponse;
use Gabormakeev\GbBlogApi\Http\Request;
use Gabormakeev\GbBlogApi\Exceptions\HttpException;
use Gabormakeev\GbBlogApi\Repositories\PostsRepository\SqlitePostsRepository;
use Gabormakeev\GbBlogApi\Repositories\UsersRepository\SqliteUsersRepository;

require_once __DIR__ . '/vendor/autoload.php';

$request = new Request($_GET, $_SERVER);

try {
    $path = $request->path();
} catch (HttpException) {
    (new ErrorResponse)->send();
    return;
}

$routes = [
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
];

if (!array_key_exists($path, $routes)) {
    (new ErrorResponse('Not found'))->send();
    return;
}

$action = $routes[$path];

try {
    $response = $action->handle($request);
} catch (AppException $e) {
    (new ErrorResponse($e->getMessage()))->send();
}

$response->send();
