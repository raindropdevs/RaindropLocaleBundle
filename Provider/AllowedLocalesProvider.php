<?php

namespace Raindrop\LocaleBundle\Provider;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

/**
 * AllowedLocales Provider
 */
class AllowedLocalesProvider
{
    /**
     * @var LoggerInterface $logger
     */
    protected $logger;

    /**
     * @var EntityManager $entityManager
     */
    protected $entityManager;

    /**
     * @var array
     */
    private $allowedLocales;

    /**
     * Constructor.
     *
     * @param LoggerInterface $logger
     * @param EntityManager   $entityManager
     * @param array           $allowedLocales List of allowed locales from configuration
     */
    public function __construct(LoggerInterface $logger, EntityManager $entityManager, array $allowedLocales = array())
    {
        $this->logger = $logger;
        $this->em = $entityManager;
        $this->allowedLocales = $allowedLocales;
    }

    /**
     * Returns the list of allowed locale
     *
     * @return array $allowedLocales List of allowed locales
     */
    public function getAllowedLocales()
    {
        if (!empty ($this->allowedLocales)) {
            return $this->allowedLocales;
        }

        return $this->getAllowedLocalesFromDatabase();
    }

    /**
     * Returns the configuration of allowed locales
     *
     * @return array
     */
    public function getAllowedLocalesFromDatabase()
    {
        // find countries locale
        $country = $this->em->getRepository('RaindropLocaleBundle:Country')
                ->findAllowedLocales();

        // find international countries locale
        $international = $this->em->getRepository('RaindropLocaleBundle:International')
                ->findAllowedLocales();

        $result = array_merge($country, $international);

        return $result;
    }

    /**
     * Returns the allowed countries
     *
     * @return array
     */
    public function getAllowedCountries()
    {
        $result = $this->em->getRepository('RaindropLocaleBundle:Country')
                ->findAllowedCountries();

        return $result;
    }

    /**
     * Returns the default language of a country
     *
     * @param string $countryCode
     */
    public function getDefaultLanguageByCountry($countryCode)
    {
        $result = $this->em->getRepository('RaindropLocaleBundle:Country')
                ->findDefaultLanguageByCountryCode($countryCode);

        return $result;
    }

    /**
     * Returns the allowed international countries
     *
     * @return array
     */
    public function getAllowedInternationalCountries()
    {
        $result = $this->em->getRepository('RaindropLocaleBundle:International')
                ->findAllowedInternationalCountries();

        return $result;
    }

    /**
     * Returns the language of a international country
     *
     * @param string $countrycode
     */
    public function getLanguageByInternationalCountry($countrycode)
    {
        $result = $this->em->getRepository('RaindropLocaleBundle:Country')
                ->findLanguageByInternationalCountry($countryCode);

        return $result;
    }

    /**
     * Returns the list of a countries
     *
     * @return array
     */
    public function getCountryList()
    {
        $country = $this->em->getRepository('RaindropLocaleBundle:Country')
                ->findCountryList();

        $international = $this->em->getRepository('RaindropLocaleBundle:International')
                ->findCountryList();

        $result = array_merge($country, $international);

        return $result;
    }
}
