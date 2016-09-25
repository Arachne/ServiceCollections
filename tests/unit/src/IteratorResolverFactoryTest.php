<?php

namespace Tests\Unit;

use Arachne\ServiceCollections\IteratorFactory;
use Arachne\ServiceCollections\IteratorResolverFactory;
use ArrayIterator;
use Closure;
use Codeception\Test\Unit;
use Eloquent\Phony\Mock\Handle\InstanceHandle;
use Eloquent\Phony\Phpunit\Phony;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class IteratorResolverFactoryTest extends Unit
{
    /**
     * @var Closure
     */
    private $resolver;

    /**
     * @var InstanceHandle
     */
    private $factoryHandle;

    protected function _before()
    {
        $services = [
            'valid' => [
                'service1',
            ],
        ];

        $this->factoryHandle = Phony::mock(IteratorFactory::class);
        $factory = new IteratorResolverFactory($this->factoryHandle->get());
        $this->resolver = $factory->create($services);
    }

    public function testValid()
    {
        $this->factoryHandle
            ->create
            ->returns(new ArrayIterator([(object) ['service1']]));

        self::assertEquals([(object) ['service1']], iterator_to_array(call_user_func($this->resolver, 'valid')));

        $this->factoryHandle
            ->create
            ->once()
            ->calledWith(['service1']);
    }

    public function testInvalid()
    {
        self::assertSame(null, call_user_func($this->resolver, 'invalid'));
    }
}
