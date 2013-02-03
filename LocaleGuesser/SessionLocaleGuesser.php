<?php

namespace Raindrop\LocaleBundle\LocaleGuesser;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Raindrop\LocaleBundle\Validator\MetaValidator;

/**
 * Locale Guesser for retrieving a previously deteced locale from the session
 *
 * @author Matthias Breddin <mb@Raindrop.com>
 */
class SessionLocaleGuesser implements LocaleGuesserInterface
{
    /**
     * @var string
     */
    private $sessionVariable;

    /**
     * @var string
     */
    private $identifiedLocale;

    /**
     * @var MetaValidator
     */
    private $metaValidator;
    /**
     * @var Session
     */
    private $session;

    /**
     * Constructor
     *
     * @param Session       $session         Session
     * @param MetaValidator $metaValidator   MetaValidator
     * @param string        $sessionVariable Key value for the Session
     */
    public function __construct(Session $session, MetaValidator $metaValidator, $sessionVariable = 'Raindrop_locale')
    {
        $this->metaValidator = $metaValidator;
        $this->session = $session;
        $this->sessionVariable = $sessionVariable;
    }

    /**
     * Guess the locale based on the session variable
     *
     * @param Request $request
     *
     * @return boolean
     */
    public function guessLocale(Request $request)
    {
        if ($this->session->has($this->sessionVariable)) {
            $locale = $this->session->get($this->sessionVariable);
            if (!$this->metaValidator->isAllowed($locale)) {
                return false;
            }
            $this->identifiedLocale = $this->session->get($this->sessionVariable);

            return true;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getIdentifiedLocale()
    {
        if (null === $this->identifiedLocale) {
            return false;
        }

        return $this->identifiedLocale;
    }

    /**
     * Sets the locale in the session
     *
     * @param string $locale Locale
     * @param bool   $force  Force write session
     */
    public function setSessionLocale($locale, $force = false)
    {
        if (!$this->session->has($this->sessionVariable) || $force) {
            $this->session->set($this->sessionVariable, $locale);
        }
    }
}
