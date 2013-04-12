<?php

namespace Raindrop\LocaleBundle\Validator;

use Symfony\Component\Validator\Validator;
use Raindrop\LocaleBundle\Validator\Locale;
use Raindrop\LocaleBundle\Validator\LocaleAllowed;

/**
 * This Metavalidator uses the LocaleAllowed and Locale validators for checks inside a guesser
 */
class MetaValidator
{
    /**
     * @var Validator
     */
    private $validator;

    /**
     * Constructor
     *
     * @param Validator $validator
     */
    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Checks if a locale is allowed and valid
     *
     * @param string $locale
     *
     * @return bool
     */
    public function isAllowed($locale)
    {
        $international = strpos($locale, $this->internationalCountryCode);

        if ($international !== false) {
            // find the correct locale from international locale
            $locale = substr($locale, 0, strpos($locale, '_'));

            $errorListLocale  = $this->validator->validateValue($locale, new Locale);

            return count($errorListLocale) == 0;
        }

        $errorListLocale  = $this->validator->validateValue($locale, new Locale);
        $errorListLocaleAllowed = $this->validator->validateValue($locale, new LocaleAllowed);

        return (count($errorListLocale) == 0 && count($errorListLocaleAllowed) == 0);
    }

    /**
     * Checks if a locale is valid
     *
     * @param string $locale
     *
     * @return bool
     */
    public function isAValid($locale)
    {
        $errorListLocale  = $this->validator->validateValue($locale, new Locale);

        return (count($errorListLocale) == 0);
    }

    /**
     * DI Setter for the international_country_code
     *
     * @param string $chooseCountryRoute
     */
    public function setGeoipParameters($internationalCountryCode)
    {
        $this->internationalCountryCode = $internationalCountryCode;
    }
}
