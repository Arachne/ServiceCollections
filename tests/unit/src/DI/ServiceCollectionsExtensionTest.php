<?php

declare(strict_types=1);

namespace Tests\Unit\DI;

use Arachne\ServiceCollections\DI\ServiceCollectionsExtension;
use Codeception\Test\Unit;
use DateTime;
use DateTimeZone;
use Eloquent\Phony\Phpunit\Phony;
use Nette\DI\Compiler;
use Nette\Utils\AssertionException;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class ServiceCollectionsExtensionTest extends Unit
{
    /**
     * @var ServiceCollectionsExtension
     */
    private $extension;

    protected function _before()
    {
        $this->extension = new ServiceCollectionsExtension();
        $this->extension->setCompiler(Phony::mock(Compiler::class)->get(), 'service_collections');
    }

    public function testGetCollection()
    {
        self::assertSame('service_collections.1.tag', $this->extension->getCollection(ServiceCollectionsExtension::TYPE_RESOLVER, 'tag'));
        self::assertSame('service_collections.1.tag', $this->extension->getCollection(ServiceCollectionsExtension::TYPE_RESOLVER, 'tag', DateTime::class));
        self::assertSame('service_collections.1.tag', $this->extension->getCollection(ServiceCollectionsExtension::TYPE_RESOLVER, 'tag', DateTime::class));
    }

    /**
     * @dataProvider dataForCollectionException
     *
     * @param int    $type
     * @param string $message
     */
    public function testGetCollectionException($type, $message)
    {
        $service = sprintf('service_collections.%d.tag', $type);

        self::assertSame($service, $this->extension->getCollection($type, 'tag', DateTime::class));
        self::assertSame($service, $this->extension->getCollection($type, 'tag'));

        $this->expectException(AssertionException::class);
        $this->expectExceptionMessage($message.' for tag "tag" already exists with implement type "DateTime".');

        $this->extension->getCollection($type, 'tag', DateTimeZone::class);
    }

    public function testOverrideCollection()
    {
        $stub = Phony::stub();
        $stub->returns('collection_override');

        $this->extension->overrideCollection(
            ServiceCollectionsExtension::TYPE_RESOLVER,
            'tag',
            $stub
        );

        $stub->once()->calledWith('service_collections.1.tag');

        self::assertSame('collection_override', $this->extension->getCollection(ServiceCollectionsExtension::TYPE_RESOLVER, 'tag'));
    }

    /**
     * @dataProvider dataForCollectionException
     *
     * @param int    $type
     * @param string $message
     */
    public function testOverrideCollectionException($type, $message)
    {
        $service = sprintf('service_collections.%d.tag', $type);

        self::assertSame($service, $this->extension->getCollection($type, 'tag', DateTime::class));

        $this->expectException(AssertionException::class);
        $this->expectExceptionMessage(
            sprintf(
                '%s for tag "tag" already exists. Try moving the extension that overrides it immediately after "%s".',
                $message,
                'Arachne\ServiceCollections\DI\ServiceCollectionsExtension'
            )
        );

        $this->extension->overrideCollection($type, 'tag', Phony::stub());
    }

    public function dataForCollectionException(): array
    {
        return [
            [
                'type' => ServiceCollectionsExtension::TYPE_RESOLVER,
                'message' => 'Resolver',
            ],
            [
                'type' => ServiceCollectionsExtension::TYPE_ITERATOR,
                'message' => 'Iterator',
            ],
            [
                'type' => ServiceCollectionsExtension::TYPE_ITERATOR_RESOLVER,
                'message' => 'Iterator resolver',
            ],
            [
                'type' => 0,
                'message' => '',
            ],
        ];
    }
}
