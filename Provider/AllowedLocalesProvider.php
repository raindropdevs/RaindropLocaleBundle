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
     * Constructor.
     *
     * @param LoggerInterface $logger
     * @param EntityManager   $entityManager
     */
    public function __construct(LoggerInterface $logger, EntityManager $entityManager)
    {
        $this->logger = $logger;
        $this->em = $entityManager;
    }

    /**
     * Returns the configuration of allowed locales
     *
     * @return array
     */
    public function getAllowedLocalesFromDatabase()
    {
        $result = $this->em->getRepository('RaindropLocaleBundle:Country')
                ->findAllowedLocales();

        return $result;
    }

    /**
     * Returns the allowed countries
     *
     * @return array
     */
    public function getAllowedCountriesFromDatabase()
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
    public function getAllowedInternationalCountriesFromDatabase()
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
}
