<?php

namespace Gabormakeev\GbBlogApi\UnitTests\Container;

use Gabormakeev\GbBlogApi\Container\DIContainer;
use Gabormakeev\GbBlogApi\Exceptions\NotFoundException;
use Gabormakeev\GbBlogApi\Repositories\UsersRepository\InMemoryUsersRepository;
use Gabormakeev\GbBlogApi\Repositories\UsersRepository\UsersRepositoryInterface;
use PHPUnit\Framework\TestCase;

class DIContainerTest extends TestCase
{
    public function testItThrowsAnExceptionIfCannotResolveType(): void
    {
        $container = new DIContainer();

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(
            'Cannot resolve type: Gabormakeev\GbBlogApi\UnitTests\Container\SomeClass'
        );

        $container->get(SomeClass::class);
    }

    public function testItResolvesClassWithoutDependencies(): void
    {
        $container = new DIContainer();

        $object = $container->get(SomeClassWithoutDependencies::class);

        $this->assertInstanceOf(SomeClassWithoutDependencies::class, $object);
    }

    public function testItResolvesClassByContract(): void
    {
        $container = new DIContainer();

        $container->bind(
            UsersRepositoryInterface::class,
            InMemoryUsersRepository::class
        );

        $object = $container->get(UsersRepositoryInterface::class);

        $this->assertInstanceOf(InMemoryUsersRepository::class, $object);
    }

    public function testItReturnsPredefinedObject(): void
    {
        $container = new DIContainer();

        $container->bind(
            SomeClassWithParameter::class,
            new SomeClassWithParameter(42)
        );

        $object = $container->get(SomeClassWithParameter::class);

        $this->assertInstanceOf(SomeClassWithParameter::class, $object);
        $this->assertSame(42, $object->getValue());
    }

    public function testItResolvesClassWithDependencies(): void
    {
        $container = new DIContainer();

        $container->bind(SomeClassWithParameter::class, new SomeClassWithParameter(42));

        $object = $container->get(ClassDependingOnAnother::class);

        $this->assertInstanceOf(ClassDependingOnAnother::class, $object);
    }
}
