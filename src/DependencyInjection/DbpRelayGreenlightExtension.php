<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\DependencyInjection;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class DbpRelayGreenlightExtension extends ConfigurableExtension implements PrependExtensionInterface
{
    /**
     * @return void
     *
     * @throws \Exception
     */
    public function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $this->extendArrayParameter(
            $container, 'api_platform.resource_class_directories', [__DIR__.'/../Entity']);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('services.yaml');

        $cacheDef = $container->register('dbp.relay.cache.greenlight', FilesystemAdapter::class);
        $cacheDef->setArguments(['greenlight', 3600, '%kernel.cache_dir%/dbp/greenlight']);
        $cacheDef->addTag('cache.pool');

        $definition = $container->getDefinition('Dbp\Relay\GreenlightBundle\Service\GreenlightService');
        $definition->addMethodCall('setCache', [$cacheDef]);
    }

    private function extendArrayParameter(ContainerBuilder $container, string $parameter, array $values): void
    {
        if (!$container->hasParameter($parameter)) {
            $container->setParameter($parameter, []);
        }
        $oldValues = $container->getParameter($parameter);
        assert(is_array($oldValues));
        $container->setParameter($parameter, array_merge($oldValues, $values));
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container): void
    {
        $configs = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration(new Configuration(), $configs);

//        foreach (['doctrine', 'doctrine_migrations'] as $extKey) {
//            if (!$container->hasExtension($extKey)) {
//                throw new \Exception("'".$this->getAlias()."' requires the '$extKey' bundle to be loaded!");
//            }
//        }

        if (isset($container->getExtensions()['doctrine'])) {
            $container->prependExtensionConfig('doctrine', [
                'dbal' => [
                    'connections' => [
                        'dbp_relay_greenlight_bundle' => [
                            'url' => $config['database_url'] ?? '',
                        ],
                    ],
                ],
                'orm' => [
                    'entity_managers' => [
                        'dbp_relay_greenlight_bundle' => [
                            'naming_strategy' => 'doctrine.orm.naming_strategy.underscore_number_aware',
                            'connection' => 'dbp_relay_greenlight_bundle',
                            'mappings' => [
                                'DbpRelayGreenlightBundle' => null,
                            ],
                        ],
                    ],
                ],
            ]);
        }

        if (isset($container->getExtensions()['doctrine_migrations'])) {
            $container->prependExtensionConfig('doctrine_migrations', [
                'migrations_paths' => [
                    'Dbp\Relay\GreenlightBundle\Migrations' => 'vendor/dbp/relay-greenlight-bundle/src/Migrations',
                ],
            ]);
        }
    }
}
