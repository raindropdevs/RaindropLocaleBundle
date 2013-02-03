<?php

namespace Raindrop\LocaleBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compilerpass Class
 *
 */
class GuesserCompilerPass implements CompilerPassInterface
{
    /**
     * Compilerpass for Locale Guessers
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('raindrop_locale.guesser_manager')) {
            return;
        }

        $definition = $container->getDefinition('raindrop_locale.guesser_manager');
        $taggedServiceIds = $container->findTaggedServiceIds('raindrop_locale.guesser');
        $neededServices = $container->getParameter('raindrop_locale.guessing_order');

        foreach ($taggedServiceIds as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                if (in_array($attributes['alias'], $neededServices)) {
                    $definition->addMethodCall('addGuesser', array(new Reference($id), $attributes["alias"]));
                }
            }
        }
    }
}

