<?php

/*
 * This file is part of Monsieur Biz' Media Manager plugin for Sylius.
 *
 * (c) Monsieur Biz <sylius@monsieurbiz.com>
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MonsieurBiz\SyliusMediaManagerPlugin\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class MonsieurBizSyliusMediaManagerExtension extends Extension implements PrependExtensionInterface
{
    /**
     * @inheritdoc
     */
    public function load(array $config, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('services.yaml');
    }

    public function prepend(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('twig_component', [
            'defaults' => [
                'MonsieurBiz\SyliusMediaManagerPlugin\Components\\' => [
                    'template_directory' => '@MonsieurBizSyliusMediaManagerPlugin/components/',
                    'name_prefix' => 'MediaManager',
                ],
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    //    public function getAlias(): string
    //    {
    //        return str_replace('monsieur_biz', 'monsieurbiz', parent::getAlias());
    //    }
}
