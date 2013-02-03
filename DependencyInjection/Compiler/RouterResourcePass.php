<?php

namespace Raindrop\LocaleBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * This pass adds the Raindrop Locale route when raindrop_locale.switcher.use_controller is true.
 *
 */
class RouterResourcePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->getParameter('raindrop_locale.switcher.use_controller') || !$container->getParameter('router.resource')) {
            return;
        }

        $file = $container->getParameter('kernel.cache_dir').'/raindrop_locale/routing.yml';

        if (!is_dir($dir = dirname($file))) {
            mkdir($dir, 0777, true);
        }

        file_put_contents($file, Yaml::dump(array(
            '_raindrop_locale' => array('resource' => '@RaindropLocaleBundle/Resources/config/routing.yml', 'prefix' => '/'),
            '_app'     => array('resource' => $container->getParameter('router.resource'))
        )));

        $container->setParameter('router.resource', $file);
    }
}