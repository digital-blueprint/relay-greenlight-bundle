<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class DbpRelayGreenlightExtension extends ConfigurableExtension
{
    public function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $this->extendArrayParameter(
            $container, 'api_platform.resource_class_directories', [__DIR__.'/../Entity']);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('services.yaml');

        // Inject the config value into the MyCustomService service
        $definition = $container->getDefinition('Dbp\Relay\GreenlightBundle\Service\MyCustomService');
        $definition->addArgument($mergedConfig['secret_token']);
    }

    private function extendArrayParameter(ContainerBuilder $container, string $parameter, array $values)
    {
        if (!$container->hasParameter($parameter)) {
            $container->setParameter($parameter, []);
        }
        $oldValues = $container->getParameter($parameter);
        assert(is_array($oldValues));
        $container->setParameter($parameter, array_merge($oldValues, $values));
    }
}
