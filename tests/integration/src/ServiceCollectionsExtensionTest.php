<?php

declare(strict_types=1);

namespace Tests\Integration;

use ArrayObject;
use Codeception\Test\Unit;
use Contributte\Codeception\Module\NetteDIModule;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class ServiceCollectionsExtensionTest extends Unit
{
    /**
     * @var NetteDIModule
     */
    protected $tester;

    public function testResolver(): void
    {
        $resolver = $this->tester->getContainer()->getService('arachne.serviceCollections.1.foo');

        self::assertEquals(new ArrayObject(['foo1']), call_user_func($resolver, 'name1'));
        self::assertEquals(new ArrayObject(['foo2']), call_user_func($resolver, 'name2'));
        self::assertEquals(new ArrayObject(['foo2']), call_user_func($resolver, 'name3'));
        self::assertEquals(new ArrayObject(['foo3']), call_user_func($resolver, 'name4'));
        self::assertSame(null, call_user_func($resolver, 'name5'));
        self::assertSame(null, call_user_func($resolver, 'name6'));
    }

    public function testIterator(): void
    {
        $iterator = $this->tester->getContainer()->getService('arachne.serviceCollections.2.foo');

        self::assertEquals(
            [
                new ArrayObject(['foo1']),
                new ArrayObject(['foo2']),
                new ArrayObject(['foo3']),
            ],
            iterator_to_array($iterator)
        );
    }

    public function testIteratorResolver(): void
    {
        $resolver = $this->tester->getContainer()->getService('arachne.serviceCollections.3.foo');

        self::assertEquals([new ArrayObject(['foo1'])], iterator_to_array(call_user_func($resolver, 'name1')));
        self::assertEquals([new ArrayObject(['foo2'])], iterator_to_array(call_user_func($resolver, 'name2')));
        self::assertEquals([new ArrayObject(['foo2'])], iterator_to_array(call_user_func($resolver, 'name3')));
        self::assertEquals([new ArrayObject(['foo3'])], iterator_to_array(call_user_func($resolver, 'name4')));
        self::assertSame(null, call_user_func($resolver, 'name5'));
        self::assertSame(null, call_user_func($resolver, 'name6'));
    }
}
