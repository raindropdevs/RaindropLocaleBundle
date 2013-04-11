<?php

namespace Raindrop\LocaleBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * AllowedLocalesPass Class
 *
 */
class AllowedLocalesPass implements CompilerPassInterface
{
    /**
     * AllowedLocalesPass for inject allowed_locales parameter on AllowedLocalesProvider
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasParameter('raindrop_locale.allowed_locales')) {
            $provider = $container->getDefinition('raindrop_locale.allowed_locales.provider');
            $provider->addArgument($container->getParameter('raindrop_locale.allowed_locales'));
        }
    }
}
