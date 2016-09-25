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

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class IteratorResolverFactory
{
    /**
     * @var IteratorFactory
     */
    private $iteratorFactory;

    public function __construct(IteratorFactory $iteratorFactory)
    {
        $this->iteratorFactory = $iteratorFactory;
    }

    /**
     * @param string[][]
     *
     * @return Closure
     */
    public function create(array $services)
    {
        return function ($name) use ($services) {
            return isset($services[$name]) ? $this->iteratorFactory->create($services[$name]) : null;
        };
    }
}
