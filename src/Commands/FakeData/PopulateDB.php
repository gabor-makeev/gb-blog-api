<?php

namespace Gabormakeev\GbBlogApi\Commands\FakeData;

use Gabormakeev\GbBlogApi\Comment;
use Gabormakeev\GbBlogApi\Post;
use Gabormakeev\GbBlogApi\Repositories\CommentsRepository\CommentsRepositoryInterface;
use Gabormakeev\GbBlogApi\Repositories\PostsRepository\PostsRepositoryInterface;
use Gabormakeev\GbBlogApi\Repositories\UsersRepository\UsersRepositoryInterface;
use Gabormakeev\GbBlogApi\User;
use Gabormakeev\GbBlogApi\UUID;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PopulateDB extends Command
{
    public function __construct(
        private \Faker\Generator $faker,
        private UsersRepositoryInterface $usersRepository,
        private PostsRepositoryInterface $postsRepository,
        private CommentsRepositoryInterface $commentsRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('fake-data:populate-db')
            ->setDescription('Populates DB with fake data')
            ->addOption(
                'users-number',
                'u',
                InputOption::VALUE_OPTIONAL,
                'Define the number of users to be generated',
                10
            )
            ->addOption(
                'posts-number',
                'p',
                InputOption::VALUE_OPTIONAL,
                'Define the number of posts to be generated per user',
                20

            );
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $users = [];

        $usersNumber = (int)$input->getOption('users-number');
        $postsNumber = (int)$input->getOption('posts-number');
        $commentsNumber = 3;

        if (!$usersNumber || !$postsNumber) {
            $output->writeln('Optional users-number (u) and posts-number (p) options must be positive integers');
            return Command::FAILURE;
        }

        for ($i = 0; $i < $usersNumber; $i++) {
            $user = $this->createFakeUser();
            $users[] = $user;
            $output->writeln('User created: ' . $user->getUsername());
        }

        foreach ($users as $user) {
            for ($i = 0; $i < $postsNumber; $i++) {
                $post = $this->createFakePost($user);
                $output->writeln('Post created: ' . $post->getTitle());

                for ($j = 0; $j < $commentsNumber; $j++) {
                    $randomUser = $users[array_rand($users)];
                    $comment = $this->createFakeComment($post, $randomUser);
                    $output->writeln('Comment created: ' . $comment->getUuid());
                }
            }
        }

        return Command::SUCCESS;
    }

    private function createFakeUser(): User
    {
        $user = User::createFrom(
            $this->faker->userName,
            $this->faker->password,
            $this->faker->firstName,
            $this->faker->lastName
        );

        $this->usersRepository->save($user);

        return $user;
    }

    private function createFakePost(User $author): Post
    {
        $post = new Post(
            UUID::random(),
            $author->getUuid(),
            $this->faker->sentence(6, true),
            $this->faker->realText
        );

        $this->postsRepository->save($post);

        return $post;
    }

    private function createFakeComment(Post $post, User $author): Comment
    {
        $comment = new Comment(
            UUID::random(),
            $author->getUuid(),
            $post->getUuid(),
            $this->faker->sentence(10, true)
        );

        $this->commentsRepository->save($comment);

        return $comment;
    }
}
