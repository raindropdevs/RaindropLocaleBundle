<?php

namespace Raindrop\LocaleBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Raindrop\LocaleBundle\DependencyInjection\Compiler\GuesserCompilerPass;
use Raindrop\LocaleBundle\DependencyInjection\Compiler\RouterResourcePass;
use Raindrop\LocaleBundle\DependencyInjection\Compiler\AllowedLocalesPass;

class RaindropLocaleBundle extends Bundle
{
    /**
     * Add CompilerPass
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new GuesserCompilerPass);
        $container->addCompilerPass(new RouterResourcePass);
        $container->addCompilerPass(new AllowedLocalesPass);
    }
}
