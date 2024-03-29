<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\DependencyInjection;

use Dbp\Relay\CoreBundle\Extension\ExtensionTrait;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class DbpRelayGreenlightExtension extends ConfigurableExtension implements PrependExtensionInterface
{
    use ExtensionTrait;

    public function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $this->addResourceClassDirectory($container, __DIR__.'/../Entity');

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('services.yaml');
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container): void
    {
        $configs = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration(new Configuration(), $configs);

        foreach (['doctrine', 'doctrine_migrations'] as $extKey) {
            if (!$container->hasExtension($extKey)) {
                throw new \Exception("'".$this->getAlias()."' requires the '$extKey' bundle to be loaded!");
            }
        }

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
                            'dbp_relay_greenlight' => [
                                'type' => 'annotation',
                                'dir' => __DIR__.'/../Entity',
                                'prefix' => 'Dbp\Relay\GreenlightBundle\Entity',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $this->registerEntityManager($container, 'dbp_relay_greenlight_bundle');

        $container->prependExtensionConfig('doctrine_migrations', [
            'migrations_paths' => [
                'Dbp\Relay\GreenlightBundle\Migrations' => __DIR__.'/../Migrations',
            ],
        ]);
    }
}
