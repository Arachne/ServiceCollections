<?php

declare(strict_types=1);

namespace Tests\Unit\DI;

use Arachne\ServiceCollections\DI\ServiceCollectionsExtension;
use Codeception\Test\Unit;
use DateTime;
use DateTimeZone;
use Nette\DI\Compiler;
use Nette\DI\ContainerBuilder;
use Nette\Utils\AssertionException;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class ServiceCollectionsExtensionExceptionsTest extends Unit
{
    /**
     * @var ServiceCollectionsExtension
     */
    private $extension;

    /**
     * @var ContainerBuilder
     */
    private $builder;

    protected function _before(): void
    {
        $compiler = new Compiler();
        $this->extension = new ServiceCollectionsExtension();
        $this->extension->setCompiler($compiler, 'service_collections');
        $this->builder = $compiler->getContainerBuilder();
    }

    public function testImplementTypeException(): void
    {
        $this->extension->getCollection(ServiceCollectionsExtension::TYPE_RESOLVER, 'tag', DateTimeZone::class);
        $this->builder->addDefinition('timezone')
            ->setClass(DateTime::class)
            ->addTag('tag');

        $this->extension->loadConfiguration();

        try {
            $this->extension->beforeCompile();
            self::fail();
        } catch (AssertionException $e) {
            self::assertSame('Service "timezone" is not an instance of "DateTimeZone".', $e->getMessage());
        }
    }

    public function testNoResolverName(): void
    {
        $this->extension->getCollection(ServiceCollectionsExtension::TYPE_RESOLVER, 'tag', DateTimeZone::class);

        $this->builder->addDefinition('timezone')
            ->setClass(DateTimeZone::class)
            ->addTag('tag');

        $this->extension->loadConfiguration();

        try {
            $this->extension->beforeCompile();
            self::fail();
        } catch (AssertionException $e) {
            self::assertSame('Service "timezone" has no resolver name for tag "tag".', $e->getMessage());
        }
    }

    public function testDuplicateResolverName(): void
    {
        $this->extension->getCollection(ServiceCollectionsExtension::TYPE_RESOLVER, 'tag', DateTimeZone::class);

        $this->builder->addDefinition('timezone')
            ->setClass(DateTimeZone::class)
            ->addTag('tag', ['default']);

        $this->builder->addDefinition('timezone_duplicate')
            ->setClass(DateTimeZone::class)
            ->addTag(
                'tag',
                [
                    ServiceCollectionsExtension::ATTRIBUTE_RESOLVER => ['default'],
                ]
            );

        $this->extension->loadConfiguration();

        try {
            $this->extension->beforeCompile();
            self::fail();
        } catch (AssertionException $e) {
            self::assertSame('Services "timezone" and "timezone_duplicate" both have resolver name "default" for tag "tag".', $e->getMessage());
        }
    }

    public function testNoResolverIteratorName(): void
    {
        $this->extension->getCollection(ServiceCollectionsExtension::TYPE_ITERATOR_RESOLVER, 'tag', DateTimeZone::class);

        $this->builder->addDefinition('timezone')
            ->setClass(DateTimeZone::class)
            ->addTag('tag');

        $this->extension->loadConfiguration();

        try {
            $this->extension->beforeCompile();
            self::fail();
        } catch (AssertionException $e) {
            self::assertSame('Service "timezone" has no iterator resolver name for tag "tag".', $e->getMessage());
        }
    }
}
