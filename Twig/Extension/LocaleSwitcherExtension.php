<?php

namespace Raindrop\LocaleBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Raindrop\LocaleBundle\Switcher\TargetInformationBuilder;

/**
 * LocaleSwitcherExtension
 * 
 */
class LocaleSwitcherExtension extends \Twig_Extension
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     *
     * @return array The added functions
     */
    public function getFunctions()
    {
        return array(
            'locale_switcher' => new \Twig_Function_Method($this, 'renderSwitcher', array('is_safe' => array('html')))
        );
    }

    /**
     *
     * @return string The name of the extension
     */
    public function getName()
    {
        return 'locale_switcher';
    }

    /**
     * @param string $route      A route name for which the switch has to be made
     * @param array  $parameters
     */
    public function renderSwitcher($route = null, $parameters = array(), $template = null)
    {
        $showCurrentLocale = $this->container->getParameter('raindrop_locale.switcher.show_current_locale');
        $useController = $this->container->getParameter('raindrop_locale.switcher.use_controller');
        $allowedLocales = $this->container->getParameter('raindrop_locale.allowed_locales');
        $request = $this->container->get('request');
        $router = $this->container->get('router');

        $infosBuilder = new TargetInformationBuilder($request, $router, $allowedLocales, $showCurrentLocale, $useController);

        $infos = $infosBuilder->getTargetInformations($route, $parameters);

        return $this->container->get('raindrop_locale.switcher_helper')->renderSwitch($infos, $template);
    }
}
