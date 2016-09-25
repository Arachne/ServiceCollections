<?php

namespace Tests\Integration\Fixtures;

use Arachne\ServiceCollections\DI\ServiceCollectionsExtension;
use Nette\DI\CompilerExtension;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class TestExtension extends CompilerExtension
{
    public function loadConfiguration()
    {
        $serviceCollection = current($this->compiler->getExtensions(ServiceCollectionsExtension::class));

        $serviceCollection->getCollection(ServiceCollectionsExtension::TYPE_RESOLVER, 'foo', 'ArrayObject');
        $serviceCollection->getCollection(ServiceCollectionsExtension::TYPE_ITERATOR, 'foo', 'ArrayObject');
        $serviceCollection->getCollection(ServiceCollectionsExtension::TYPE_ITERATOR_RESOLVER, 'foo', 'ArrayObject');

        $serviceCollection->getCollection(ServiceCollectionsExtension::TYPE_RESOLVER, 'bar');
    }
}
