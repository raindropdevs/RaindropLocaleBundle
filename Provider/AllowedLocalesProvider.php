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
     * @param EntityManager $entityManager
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
        $query = $this->em->createQuery('SELECT c, dl, l FROM RaindropLocaleBundle:Country c LEFT JOIN c.defaultLanguage dl LEFT JOIN c.languages l WHERE c.enabled = true')->getResult();
        
        foreach ($query as $country) {
            $result[] = $country->getDefaultLanguage()->getCode().'_'.$country->getCode();
            foreach ($country->getLanguages() as $language) {
                $result[] = $language->getCode().'_'.$country->getCode();
            }
        }

        return $result;
    }  
    
    /**
     * Returns the allowed countries
     *
     * @return array
     */
    public function getAllowedCountriesFromDatabase()
    {
        $query = $this->em->createQuery('SELECT c FROM RaindropLocaleBundle:Country c WHERE c.enabled = true')->getResult();
        
        foreach ($query as $country) {
            $result[] = $country->getCode();
        }

        return $result;
    }   
    
    /**
     * Returns the allowed international countries
     *
     * @return array
     */
    public function getAllowedInternationalCountriesFromDatabase()
    {
        $query = $this->em->createQuery('SELECT i, c, l FROM RaindropLocaleBundle:International i LEFT JOIN i.countries c LEFT JOIN i.language l')->getResult();

        foreach ($query as $international) {
            foreach ($international->getCountries() as $country) {
                $result[] = $country->getCode();
            }
        }

        return $result;
    }      

    /**
     * Returns the default language of a country
     * 
     * @param string $country 
     */
    public function getDefaultLanguageByCountry($country) 
    {
        $query = $this->em->createQuery('SELECT c, dl FROM RaindropLocaleBundle:Country c LEFT JOIN c.defaultLanguage dl WHERE c.code = :code')->setParameter('code', $country)->getSingleResult();
        
        $result = $query->getDefaultLanguage()->getCode();

        return $result;
    }

    /**
     * Returns the language of a international country
     * 
     * @param string $country 
     */
    public function getLanguageByInternationalCountry($country) 
    {
        $query = $this->em->createQuery('SELECT i, c, l FROM RaindropLocaleBundle:International i LEFT JOIN i.countries c LEFT JOIN i.language l WHERE c.code = :code')->setParameter('code', $country)->getSingleResult();
        
        $result = $query->getLanguage()->getCode();

        return $result;
    }
}
