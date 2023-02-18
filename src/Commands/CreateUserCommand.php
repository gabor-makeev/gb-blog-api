<?php

namespace Gabormakeev\GbBlogApi\Commands;

use Gabormakeev\GbBlogApi\Exceptions\UserNotFoundException;
use Gabormakeev\GbBlogApi\Repositories\UsersRepository\UsersRepositoryInterface;
use Gabormakeev\GbBlogApi\User;
use Psr\Log\LoggerInterface;

class CreateUserCommand
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
        private LoggerInterface $logger,
    ) {}

    public function handle(Arguments $arguments): void
    {
        $this->logger->info("Create user command started");

        $username = $arguments->get('username');

        if ($this->userExists($username)) {
            $this->logger->warning("User already exists: $username");
            return;
        }

        $user = User::createFrom(
            $username,
            $arguments->get('password'),
            $arguments->get('first_name'),
            $arguments->get('last_name')
        );

        $this->usersRepository->save($user);

        $this->logger->info("User created: {$user->getUuid()}");
    }

    private function userExists(string $username): bool
    {
        try {
            $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException) {
            return false;
        }

        return true;
    }
}
