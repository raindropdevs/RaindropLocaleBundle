<?php

namespace Raindrop\LocaleBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

/**
 * Filter for the LocaleSwitchEvent
 */
class FilterLocaleSwitchEvent extends Event
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var string
     */
    protected $locale;

    /**
     * Constructor
     *
     * @param string $locale
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(Request $request, $locale)
    {
        if (!is_string($locale) || null == $locale || '' == $locale) {
            throw new \InvalidArgumentException(sprintf('Wrong type, expected \'string\' got \'%s\'', $locale));
        }

        $this->request = $request;
        $this->locale = $locale;
    }

    /**
     * Returns the request
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Returns the locale string
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }
}