<?php

namespace Tests\Unit;

use Arachne\ServiceCollections\ResolverFactory;
use Closure;
use Codeception\Test\Unit;
use Eloquent\Phony\Mock\Handle\InstanceHandle;
use Eloquent\Phony\Phpunit\Phony;
use Nette\DI\Container;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class ResolverFactoryTest extends Unit
{
    /**
     * @var Closure
     */
    private $resolver;

    /**
     * @var InstanceHandle
     */
    private $containerHandle;

    protected function _before()
    {
        $services = [
            'valid' => 'service1',
        ];

        $this->containerHandle = Phony::mock(Container::class);
        $factory = new ResolverFactory($this->containerHandle->get());
        $this->resolver = $factory->create($services);
    }

    public function testValid()
    {
        $this->containerHandle
            ->getService
            ->returns((object) ['service1']);

        self::assertEquals((object) ['service1'], call_user_func($this->resolver, 'valid'));

        $this->containerHandle
            ->getService
            ->once()
            ->calledWith('service1');
    }

    public function testInvalid()
    {
        self::assertSame(null, call_user_func($this->resolver, 'invalid'));
    }
}
