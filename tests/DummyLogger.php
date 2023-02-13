<?php

namespace Gabormakeev\GbBlogApi\UnitTests;

use Psr\Log\LoggerInterface;
use Stringable;

final class DummyLogger implements LoggerInterface
{
    public function emergency(
        Stringable|string $message, array $context = []
    ): void
    {
        //
    }

    public function alert(
        Stringable|string $message, array $context = []
    ): void
    {
        //
    }

    public function critical(
        Stringable|string $message, array $context = []
    ): void
    {
        //
    }

    public function error(
        Stringable|string $message, array $context = []
    ): void
    {
        //
    }

    public function warning(
        Stringable|string $message, array $context = []
    ): void
    {
        echo $message;
    }

    public function notice(
        Stringable|string $message, array $context = []
    ): void
    {
        //
    }

    public function info(
        Stringable|string $message, array $context = []
    ): void
    {
        //
    }

    public function debug(
        Stringable|string $message, array $context = []
    ): void
    {
        //
    }

    public function log(
        $level, Stringable|string $message, array $context = []
    ): void
    {
        //
    }
}
