<?php

namespace Tests\Unit;

use Arachne\ServiceCollections\IteratorFactory;
use Codeception\Test\Unit;
use Eloquent\Phony\Mock\Handle\InstanceHandle;
use Eloquent\Phony\Phpunit\Phony;
use Nette\DI\Container;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class IteratorFactoryTest extends Unit
{
    /**
     * @var IteratorFactory
     */
    private $factory;

    /**
     * @var InstanceHandle
     */
    private $containerHandle;

    protected function _before()
    {
        $this->containerHandle = Phony::mock(Container::class);
        $this->factory = new IteratorFactory($this->containerHandle->get());
    }

    public function testCreate()
    {
        $this->containerHandle
            ->getService
            ->with('service1')
            ->returns((object) ['service1']);

        $this->containerHandle
            ->getService
            ->with('service2')
            ->returns((object) ['service2']);

        $services = [
            'service1',
            'service2',
        ];

        self::assertEquals(
            [
                (object) ['service1'],
                (object) ['service2'],
            ],
            iterator_to_array($this->factory->create($services))
        );

        $this->containerHandle
            ->getService
            ->once()
            ->calledWith('service1');

        $this->containerHandle
            ->getService
            ->once()
            ->calledWith('service2');
    }
}
