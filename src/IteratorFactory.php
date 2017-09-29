<?php

declare(strict_types=1);

namespace Arachne\ServiceCollections;

use ArrayIterator;
use Iterator;
use Nette\DI\Container;
use Nette\Iterators\Mapper;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class IteratorFactory
{
    /**
     * @var Container
     */
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function create(array $services): Iterator
    {
        return new Mapper(
            new ArrayIterator($services),
            function ($service) {
                return $this->container->getService($service);
            }
        );
    }
}
