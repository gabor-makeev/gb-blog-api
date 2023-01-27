<?php

use Faker\Factory;
use Gabormakeev\GbBlogApi\Comment;
use Gabormakeev\GbBlogApi\Post;
use Gabormakeev\GbBlogApi\User;

require 'vendor/autoload.php';

$faker = Factory::create();

if (!isset($argv[1]) || !in_array($argv[1], ['user', 'post', 'comment'])) {
    die('Please enter a valid argument for the cli.php script: user, post or comment' . PHP_EOL);
}

$user = new User(
    $faker->randomDigitNotNull(),
    $faker->firstName(),
    $faker->lastName()
);

switch ($argv[1]) {
    case 'user':
        echo $user . PHP_EOL;
        break;
    case 'post':
        $post = new Post(
            $faker->randomDigitNotNull(),
            $user->getId(),
            $faker->sentence(),
            $faker->realText()
        );

        echo $post . PHP_EOL;
        break;
    case 'comment':
        $post = new Post(
            $faker->randomDigitNotNull(),
            $user->getId(),
            $faker->sentence(),
            $faker->realText()
        );

        $comment = new Comment(
            $faker->randomDigitNotNull(),
            $user->getId(),
            $post->getId(),
            $faker->realText()
        );

        echo $comment . PHP_EOL;
        break;
}
