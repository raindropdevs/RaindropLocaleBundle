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
        $this->result = array();
    }
    
    /**
     * Returns the configuration of allowed locales
     *
     * @return array
     */
    public function getAllowedLocalesFromDatabase()
    {
        $query = $this->em->createQuery('SELECT c FROM RaindropLocaleBundle:Country c WHERE c.enabled = true')->getResult();
        
        foreach ($query as $country) {
            foreach ($country->getLanguages() as $language) {
                $this->result[] = $language->getCode().'_'.$country->getCode();
            }
        }

        return $this->result;
    }    
}
