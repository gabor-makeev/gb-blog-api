<?php

use Faker\Factory;
use Gabormakeev\GbBlogApi\Commands\Arguments;
use Gabormakeev\GbBlogApi\Commands\CreateUserCommand;
use Gabormakeev\GbBlogApi\Comment;
use Gabormakeev\GbBlogApi\Exceptions\AppException;
use Gabormakeev\GbBlogApi\Post;
use Gabormakeev\GbBlogApi\Repositories\CommentsRepository\SqliteCommentsRepository;
use Gabormakeev\GbBlogApi\Repositories\PostsRepository\SqlitePostsRepository;
use Gabormakeev\GbBlogApi\Repositories\UsersRepository\SqliteUsersRepository;
use Gabormakeev\GbBlogApi\UUID;

require 'vendor/autoload.php';

$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');

$usersRepository = new SqliteUsersRepository($connection);
$postsRepository = new SqlitePostsRepository($connection);
$commentsRepository = new SqliteCommentsRepository($connection);

$command = new CreateUserCommand($usersRepository);

try {
    $command->handle(Arguments::fromArgv($argv));
} catch (AppException $e) {
    echo "{$e->getMessage()}\n";
}

$faker = Factory::create();

// SqlitePostsRepository testing

$fakePost = new Post(
    UUID::random(),
    new UUID('ad6f62fb-9c77-49f2-8411-1719e6964d52'), // uuid of user with username "test" in the database
    $faker->sentence(),
    $faker->text()
);

$postsRepository->save($fakePost);

$savedPost = $postsRepository->get($fakePost->getUuid());
$savedPostAuthor = $usersRepository->get($savedPost->getAuthorUuid());

echo "{$savedPostAuthor->getUsername()} wrote a post with the title '{$savedPost->getTitle()}':\n{$savedPost->getText()}\n";

// SqliteCommentsRepository testing

$fakeComment = new Comment(
    UUID::random(),
    new UUID('24abd8c6-ba95-4b87-91b3-ba55c24a58a5'), // uuid of user with username "test_2" in the database
    $fakePost->getUuid(),
    $faker->text()
);

$commentsRepository->save($fakeComment);

$savedComment = $commentsRepository->get($fakeComment->getUuid());
$savedCommentAuthor = $usersRepository->get($savedComment->getAuthorUuid());
$savedCommentPost = $postsRepository->get($savedComment->getPostUuid());
$savedCommentPostAuthor = $usersRepository->get($savedCommentPost->getAuthorUuid());

echo "{$savedCommentAuthor->getUsername()} wrote a comment to a post with title '{$savedCommentPost->getTitle()}' (Author: {$savedCommentPostAuthor->getUsername()}):\n{$savedComment->getText()}\n";
