<?php

namespace Tests\Integration;

use ArrayObject;
use Codeception\Test\Unit;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class ServiceCollectionsExtensionTest extends Unit
{
    protected $tester;

    public function testResolver()
    {
        $resolver = $this->tester->getContainer()->getService('arachne.service_collections.1.foo');

        $this->assertEquals(new ArrayObject(['foo1']), call_user_func($resolver, 'name1'));
        $this->assertEquals(new ArrayObject(['foo2']), call_user_func($resolver, 'name2'));
        $this->assertEquals(new ArrayObject(['foo2']), call_user_func($resolver, 'name3'));
        $this->assertEquals(new ArrayObject(['foo3']), call_user_func($resolver, 'name4'));
        $this->assertSame(null, call_user_func($resolver, 'name5'));
        $this->assertSame(null, call_user_func($resolver, 'name6'));
    }

    public function testIterator()
    {
        $iterator = $this->tester->getContainer()->getService('arachne.service_collections.2.foo');

        $this->assertEquals(
            [
                new ArrayObject(['foo1']),
                new ArrayObject(['foo2']),
                new ArrayObject(['foo3']),
            ],
            iterator_to_array($iterator)
        );
    }

    public function testIteratorResolver()
    {
        $resolver = $this->tester->getContainer()->getService('arachne.service_collections.3.foo');

        $this->assertEquals([new ArrayObject(['foo1'])], iterator_to_array(call_user_func($resolver, 'name1')));
        $this->assertEquals([new ArrayObject(['foo2'])], iterator_to_array(call_user_func($resolver, 'name2')));
        $this->assertEquals([new ArrayObject(['foo2'])], iterator_to_array(call_user_func($resolver, 'name3')));
        $this->assertEquals([new ArrayObject(['foo3'])], iterator_to_array(call_user_func($resolver, 'name4')));
        $this->assertSame(null, call_user_func($resolver, 'name5'));
        $this->assertSame(null, call_user_func($resolver, 'name6'));
    }
}
