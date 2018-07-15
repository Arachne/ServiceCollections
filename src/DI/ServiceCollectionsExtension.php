<?php

declare(strict_types=1);

namespace Arachne\ServiceCollections\DI;

use Arachne\ServiceCollections\IteratorFactory;
use Arachne\ServiceCollections\IteratorResolverFactory;
use Arachne\ServiceCollections\ResolverFactory;
use Closure;
use Iterator;
use Nette\DI\CompilerExtension;
use Nette\Utils\AssertionException;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class ServiceCollectionsExtension extends CompilerExtension
{
    const TYPE_RESOLVER = 1;
    const TYPE_ITERATOR = 2;
    const TYPE_ITERATOR_RESOLVER = 3;

    const ATTRIBUTE_RESOLVER = 'arachne.service_collections.resolver';
    const ATTRIBUTE_ITERATOR_RESOLVER = 'arachne.service_collections.iterator_resolver';

    /**
     * @var array
     */
    private $services = [
        self::TYPE_RESOLVER => [],
        self::TYPE_ITERATOR => [],
        self::TYPE_ITERATOR_RESOLVER => [],
    ];

    /**
     * @var array
     */
    private $overrides = [
        self::TYPE_RESOLVER => [],
        self::TYPE_ITERATOR => [],
        self::TYPE_ITERATOR_RESOLVER => [],
    ];

    public function getCollection(int $type, string $tag, ?string $implement = null): string
    {
        if (isset($this->overrides[$type][$tag])) {
            return $this->overrides[$type][$tag];
        }

        if ($implement !== null && isset($this->services[$type][$tag]) && $this->services[$type][$tag] !== $implement) {
            throw new AssertionException(
                sprintf(
                    '%s for tag "%s" already exists with implement type "%s".',
                    $this->typeToString($type),
                    $tag,
                    $this->services[$type][$tag]
                )
            );
        }

        if (!isset($this->services[$type][$tag]) || $implement !== null) {
            $this->services[$type][$tag] = $implement;
        }

        return $this->prefix($type.'.'.$tag);
    }

    public function overrideCollection(int $type, string $tag, callable $factory): void
    {
        if (array_key_exists($tag, $this->services[$type])) {
            throw new AssertionException(
                sprintf(
                    '%s for tag "%s" already exists. Try moving the extension that overrides it immediately after "%s".',
                    $this->typeToString($type),
                    $tag,
                    get_class($this)
                )
            );
        }

        $this->overrides[$type][$tag] = $factory($this->getCollection($type, $tag));
    }

    public function loadConfiguration(): void
    {
        $builder = $this->getContainerBuilder();

        $builder->addDefinition($this->prefix('resolverFactory'))
            ->setType(ResolverFactory::class);

        $builder->addDefinition($this->prefix('iteratorFactory'))
            ->setType(IteratorFactory::class);

        $builder->addDefinition($this->prefix('iteratorResolverFactory'))
            ->setType(IteratorResolverFactory::class);
    }

    public function beforeCompile(): void
    {
        foreach ($this->services[self::TYPE_RESOLVER] as $tag => $implement) {
            $this->checkImplementTypes($tag, $implement);

            $this->addService(
                self::TYPE_RESOLVER.'.'.$tag,
                Closure::class,
                ResolverFactory::class,
                $this->processResolverServices($tag)
            );
        }

        foreach ($this->services[self::TYPE_ITERATOR] as $tag => $implement) {
            $this->checkImplementTypes($tag, $implement);

            $this->addService(
                self::TYPE_ITERATOR.'.'.$tag,
                Iterator::class,
                IteratorFactory::class,
                $this->processIteratorServices($tag)
            );
        }

        foreach ($this->services[self::TYPE_ITERATOR_RESOLVER] as $tag => $implement) {
            $this->checkImplementTypes($tag, $implement);

            $this->addService(
                self::TYPE_ITERATOR_RESOLVER.'.'.$tag,
                Closure::class,
                IteratorResolverFactory::class,
                $this->processIteratorResolverServices($tag)
            );
        }
    }

    private function checkImplementTypes(string $tag, ?string $implement): void
    {
        if ($implement === null) {
            return;
        }

        $builder = $this->getContainerBuilder();

        foreach ($builder->findByTag($tag) as $name => $attributes) {
            $class = $builder->getDefinition($name)->getClass();

            if ($class === null || ($class !== $implement && !is_subclass_of($class, $implement))) {
                throw new AssertionException(
                    sprintf('Service "%s" is not an instance of "%s".', $name, $implement)
                );
            }
        }
    }

    private function addService(string $name, string $class, string $factory, array $services): void
    {
        $this
            ->getContainerBuilder()
            ->addDefinition($this->prefix($name))
            ->setType($class)
            ->setFactory(sprintf('@%s::create', $factory), [$services])
            ->setAutowired(false);
    }

    private function processResolverServices(string $tag): array
    {
        $services = [];
        foreach ($this->getContainerBuilder()->findByTag($tag) as $key => $attributes) {
            $names = (array) (isset($attributes[self::ATTRIBUTE_RESOLVER]) ? $attributes[self::ATTRIBUTE_RESOLVER] : $attributes);

            foreach ($names as $name) {
                if (!is_string($name)) {
                    throw new AssertionException(
                        sprintf('Service "%s" has no resolver name for tag "%s".', $key, $tag)
                    );
                }

                if (isset($services[$name])) {
                    throw new AssertionException(
                        sprintf(
                            'Services "%s" and "%s" both have resolver name "%s" for tag "%s".',
                            $services[$name],
                            $key,
                            $name,
                            $tag
                        )
                    );
                }

                $services[$name] = $key;
            }
        }

        return $services;
    }

    private function processIteratorServices(string $tag): array
    {
        return array_keys($this->getContainerBuilder()->findByTag($tag));
    }

    private function processIteratorResolverServices(string $tag): array
    {
        $services = [];
        foreach ($this->getContainerBuilder()->findByTag($tag) as $key => $attributes) {
            $names = (array) (isset($attributes[self::ATTRIBUTE_ITERATOR_RESOLVER]) ? $attributes[self::ATTRIBUTE_ITERATOR_RESOLVER] : $attributes);

            foreach ($names as $name) {
                if (!is_string($name)) {
                    throw new AssertionException(
                        sprintf('Service "%s" has no iterator resolver name for tag "%s".', $key, $tag)
                    );
                }

                $services[$name][] = $key;
            }
        }

        return $services;
    }

    private function typeToString(int $type): string
    {
        switch ($type) {
            case self::TYPE_RESOLVER:
                return 'Resolver';
            case self::TYPE_ITERATOR:
                return 'Iterator';
            case self::TYPE_ITERATOR_RESOLVER:
                return 'Iterator resolver';
        }

        throw new \InvalidArgumentException();
    }
}
