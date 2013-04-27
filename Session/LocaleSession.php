<?php

namespace Raindrop\LocaleBundle\Session;

use Symfony\Component\HttpFoundation\Session\Session;

/**
 * LocaleSessionClass
 */
class LocaleSession
{

    /**
     * @var Session
     */
    private $session;
    /**
     * @var string
     */
    private $sessionVar;

    /**
     * Constructor
     *
     * @param Session $session    Session
     * @param string  $sessionVar Session config var
     */
    public function __construct(Session $session, $sessionVar = 'raindrop_locale')
    {
        $this->session = $session;
        $this->sessionVar = $sessionVar;
    }

    /**
     * Checks if the locale has changes
     *
     * @param string $locale
     *
     * @return bool
     */
    public function hasLocaleChanged($locale)
    {
        return $locale !== $this->session->get($this->sessionVar);
    }

    /**
     * Sets the locale
     *
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->session->set($this->sessionVar, $locale);
    }

    /**
     * Returns the locale
     *
     * @param string $locale
     */
    public function getLocale($locale)
    {
        $this->session->get($this->sessionVar, $locale);
    }

    /**
     * Returns the session var/key where the locale is saved in
     *
     * @return string
     */
    public function getSessionVar()
    {
        return $this->sessionVar;
    }
}
