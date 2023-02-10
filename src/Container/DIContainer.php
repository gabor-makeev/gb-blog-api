<?php

namespace Gabormakeev\GbBlogApi\Container;

use Gabormakeev\GbBlogApi\Exceptions\NotFoundException;
use Psr\Container\ContainerInterface;
use ReflectionClass;

class DIContainer implements ContainerInterface
{
    private array $resolvers = [];

    public function has(string $id): bool
    {
        try {
            $this->get($id);
        } catch (NotFoundException $e) {
            return false;
        }

        return true;
    }

    public function bind(string $type, $resolver): void
    {
        $this->resolvers[$type] = $resolver;
    }

    public function get(string $type): object
    {
        if (array_key_exists($type, $this->resolvers)) {
            $typeToCreate = $this->resolvers[$type];

            if (is_object($typeToCreate)) {
                return $typeToCreate;
            }

            return $this->get($typeToCreate);
        }

        if (!class_exists($type)) {
            throw new NotFoundException("Cannot resolve type: $type");
        }

        $reflectionClass = new ReflectionClass($type);

        $constructor = $reflectionClass->getConstructor();

        if ($constructor === null) {
            return new $type();
        }

        $parameters = [];

        foreach ($constructor->getParameters() as $parameter) {
            $parameterType = $parameter->getType()->getName();

            $parameters[] = $this->get($parameterType);
        }

        return new $type(...$parameters);
    }
}
