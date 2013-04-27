<?php

namespace Raindrop\LocaleBundle\Templating\Helper;

use Symfony\Component\Templating\Helper\Helper;
use Symfony\Component\Templating\EngineInterface;

/**
 * LocaleSwitchHelper
 */
class LocaleSwitchHelper extends Helper
{
    protected $templating;

    protected $templates = array(
        'links' => 'RaindropLocaleBundle:Switcher:switcher_links.html.twig',
        'form' => 'RaindropLocaleBundle:Switcher:switcher_form.html.twig'
    );

    protected $view;

    /**
     * Constructor
     *
     * @param EngineInterface $templating
     * @param string          $template   The Twig Template that renders the switch
     */
    public function __construct(EngineInterface $templating, $template)
    {
        $this->templating = $templating;
        $this->view = array_key_exists($template, $this->templates)
            ? $this->templates[$template] : $template;
    }

    /**
     *
     * @param array $viewParams
     */
    public function renderSwitch(array $viewParams = array(), $template = null)
    {
        $template = $template ?: $this->view;

        return $this->templating->render($template, $viewParams);
    }

    /**
     *
     * @return string The name of the helper
     */
    public function getName()
    {
        return 'locale_switch_helper';
    }
}
