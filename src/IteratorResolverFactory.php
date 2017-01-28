<?php

declare(strict_types=1);

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
    public function create(array $services): Closure
    {
        return function (string $name) use ($services) {
            return isset($services[$name]) ? $this->iteratorFactory->create($services[$name]) : null;
        };
    }
}
