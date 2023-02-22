<?php

use Gabormakeev\GbBlogApi\Commands\FakeData\PopulateDB;
use Gabormakeev\GbBlogApi\Commands\Posts\DeletePost;
use Gabormakeev\GbBlogApi\Commands\Users\CreateUser;
use Gabormakeev\GbBlogApi\Commands\Users\UpdateUser;
use Symfony\Component\Console\Application;

$container = require __DIR__ . '/bootstrap.php';

$application = new Application();

$commandsClasses = [
    CreateUser::class,
    DeletePost::class,
    UpdateUser::class,
    PopulateDB::class,
];

foreach ($commandsClasses as $commandClass) {
    $command = $container->get($commandClass);

    $application->add($command);
}

$application->run();
