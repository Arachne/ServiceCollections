<?php

declare(strict_types=1);

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

    protected function _before(): void
    {
        $this->containerHandle = Phony::mock(Container::class);
        $this->factory = new IteratorFactory($this->containerHandle->get());
    }

    public function testCreate(): void
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

        $iterator = $this->factory->create($services);

        self::assertEquals(
            [
                (object) ['service1'],
                (object) ['service2'],
            ],
            iterator_to_array($iterator)
        );

        // Make the assert twice to ensure that the iterator is rewindable.
        self::assertEquals(
            [
                (object) ['service1'],
                (object) ['service2'],
            ],
            iterator_to_array($iterator)
        );

        $this->containerHandle
            ->getService
            ->calledWith('service1');

        $this->containerHandle
            ->getService
            ->calledWith('service2');
    }
}
