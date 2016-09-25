<?php

/*
 * This file is part of the Arachne
 *
 * Copyright (c) Jáchym Toušek (enumag@gmail.com)
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 */

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

    /**
     * @return Iterator
     */
    public function create(array $services)
    {
        return new Mapper(
            new ArrayIterator($services),
            function ($service) {
                return $this->container->getService($service);
            }
        );
    }
}
