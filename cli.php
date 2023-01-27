<?php

use Faker\Factory;
use Gabormakeev\GbBlogApi\User;

require 'vendor/autoload.php';

$faker = Factory::create();

switch ($argv[1] ?? null) {
    case 'user':
        $user = new User(
            $faker->randomDigitNotNull,
            $faker->firstName(),
            $faker->lastName()
        );
        echo $user . PHP_EOL;

        break;
    default:
        die('Please enter a valid argument for the cli.php script: user' . PHP_EOL);
}
