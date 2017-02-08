<?php

/*
 * This file is part of the Arachne
 *
 * Copyright (c) Jáchym Toušek (enumag@gmail.com)
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 */

namespace Arachne\ServiceCollections;

use Closure;
use Nette\DI\Container;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class ResolverFactory
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
     * @param string[]
     *
     * @return Closure
     */
    public function create(array $services): Closure
    {
        return function (string $name) use ($services) {
            return isset($services[$name]) ? $this->container->getService($services[$name]) : null;
        };
    }
}
