<?php

use Gabormakeev\GbBlogApi\Commands\Arguments;
use Gabormakeev\GbBlogApi\Commands\CreateUserCommand;
use Gabormakeev\GbBlogApi\Exceptions\AppException;
use Gabormakeev\GbBlogApi\Repositories\UsersRepository\SqliteUsersRepository;

require 'vendor/autoload.php';

$usersRepository = new SqliteUsersRepository(
    new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
);

$command = new CreateUserCommand($usersRepository);

try {
    $command->handle(Arguments::fromArgv($argv));
} catch (AppException $e) {
    echo "{$e->getMessage()}\n";
}
