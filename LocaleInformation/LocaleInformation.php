<?php

namespace Raindrop\LocaleBundle\LocaleInformation;

use Symfony\Component\Locale\Locale;
use Raindrop\LocaleBundle\Validator\MetaValidator;
use Raindrop\LocaleBundle\Provider\AllowedLocalesProvider;

/**
 * Information about Locales
 */
class LocaleInformation
{
    private $metaValidator;
    private $allowedLocales;

    /**
     * @param MetaValidator $metaValidator  Validator
     * @param array         $allowedLocales Allowed locales from config
     */
    public function __construct(MetaValidator $metaValidator, array $allowedLocales = array())
    {
        $this->allowedLocales = $allowedLocales;
        $this->metaValidator = $metaValidator;
    }

    /**
     * Returns the configuration of allowed locales
     *
     * @return array
     */
    public function getAllowedLocalesFromConfiguration()
    {
        return $this->allowedLocales;
    }

    /**
     * Returns an array of all allowed locales based on the configuration
     *
     * @return array|bool
     */
    public function getAllAllowedLocales()
    {
        return $this->filterAllowed(Locale::getLocales());
    }

    /**
     * Returns an array of all allowed languages based on the configuration
     *
     * @return array|bool
     */
    public function getAllAllowedLanguages()
    {
        return $this->filterAllowed(Locale::getLanguages());
    }

    /**
     * Filter function which returns locales / languages
     *
     * @param array $localeList
     *
     * @return array|bool
     */
    private function filterAllowed(array $localeList)
    {
        $validator = $this->metaValidator;
        $matchLocale = function ($locale) use ($validator) {
            return $validator->isAllowed($locale);
        };
        $availableLocales = array_values(array_filter($localeList, $matchLocale));
        if (!empty($availableLocales)) {
            return $availableLocales;
        }

        return false;
    }

    /**
     * DI Setter for the AllowedLocalesProvider
     *
     * @param \Raindrop\LocaleBundle\Provider\AllowedLocalesProvider $allowedLocalesProvider
     */
    public function setAllowedLocalesProvider(AllowedLocalesProvider $allowedLocalesProvider)
    {
        $allowedLocales = $allowedLocalesProvider->getAllowedLocales();
        if (!empty ($allowedLocales)) $this->allowedLocales = $allowedLocales;
    }
}
