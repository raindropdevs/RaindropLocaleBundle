<?php

namespace Raindrop\LocaleBundle\LocaleGuesser;

use Symfony\Component\HttpFoundation\Request;
use Raindrop\LocaleBundle\Validator\MetaValidator;

/**
 * Locale Guesser for detecing Sonata admin and prevent useless work
 */
class SonataLocaleGuesser implements LocaleGuesserInterface
{
    /**
     * @var string
     */
    private $identifiedLocale;

    /**
     * @var MetaValidator
     */
    private $metaValidator;

    /**
     * Constructor
     *
     * @param MetaValidator $metaValidator MetaValidator
     */
    public function __construct(MetaValidator $metaValidator)
    {
        $this->metaValidator = $metaValidator;
    }

    /**
     * Method that guess the locale based on the Router parameters
     *
     * @param Request $request
     *
     * @return boolean True if locale is detected, false otherwise
     */
    public function guessLocale(Request $request)
    {
        if ($sonata = $request->attributes->get('_sonata_admin')) {
            if (strpos($sonata, 'preview') === false) {
                $this->identifiedLocale = $request->getPreferredLanguage();
            }

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
}
