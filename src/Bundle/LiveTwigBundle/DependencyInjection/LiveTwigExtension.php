<?php

namespace Symfony\Bundle\LiveTwigBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\LiveTwig\MessageHandler\LiveTwigUpdateHandler;

class LiveTwigExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('live_twig.mercure_public_url', $config['mercure_public_url']);

        $loader = new XmlFileLoader($container, new FileLocator(\dirname(__DIR__).'/Resources/config'));
        $loader->load('services.xml');

        $container->getDefinition(LiveTwigUpdateHandler::class)
            ->setArgument(0, new Reference($config['mercure_publisher']));
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        // Auto-enable the fragments
        $container->prependExtensionConfig('framework', [
            'fragments' => [
                'path' => '/_fragment',
            ],
        ]);
    }
}
