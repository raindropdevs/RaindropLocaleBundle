<?php

namespace Raindrop\LocaleBundle\LocaleGuesser;

use Symfony\Component\HttpFoundation\Request;
use Raindrop\LocaleBundle\Validator\MetaValidator;

/**
 * Locale Guesser for detecing the locale in the router
 *
 * @author Matthias Breddin <mb@Raindrop.com>
 * @author Christophe Willemsen <willemsen.christophe@gmail.com>
 */
class RouterLocaleGuesser implements LocaleGuesserInterface
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
        $localeValidator = $this->metaValidator;
        if ($locale = $request->attributes->get('_locale')) {
            if ($localeValidator->isAllowed($locale)) {
                $this->identifiedLocale = $locale;
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
