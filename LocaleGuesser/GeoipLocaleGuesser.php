<?php

namespace Raindrop\LocaleBundle\LocaleGuesser;

use Symfony\Component\HttpFoundation\Request;
use Raindrop\LocaleBundle\Validator\MetaValidator;
use Raindrop\GeoipBundle\Manager\GeoipManager;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Raindrop\LocaleBundle\RaindropLocaleBundleEvents;

/**
 * Locale Guesser for detecing the locale from the client IP
 */
class GeoipLocaleGuesser implements LocaleGuesserInterface
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
     * @var GeoipManager
     */
    private $geoip;
    
    /**
     * Constructor
     *
     * @param MetaValidator $metaValidator MetaValidator
     * @param GeoipManager $geoip          GeoipManager
     */    
    public function __construct(MetaValidator $metaValidator, GeoipManager $geoip)
    {
        $this->metaValidator = $metaValidator;
        $this->geoip = $geoip;
    }    
    
    /**
     * Guess the locale based on the client ip
     *
     * @param Request $request
     *
     * @return boolean
     */ 
    public function guessLocale(Request $request)
    {
        $validator = $this->metaValidator;
        $geoip = $this->geoip;

        $clientIp = $request->getClientIp();
        // Get the country code / locale
        $countryCode = $geoip->getCountryCode($clientIp);

        if (empty($countryCode)) {
            return false;
        }        

        // If the country code is valid, return the locale.
        if ($validator->isValid($countryCode)) {
            $this->identifiedLocale = $countryCode;

            return true;
        }
        
        return false;
    }   
    
    /**
     * {@inheritDoc}
     */
    public function getIdentifiedLocale()
    {
        return $this->identifiedLocale;
    }   
    
    /**
     * DI Setter for the EventDispatcher
     *
     * @param \Symfony\Component\EventDispatcher\EventDispatcher $dispatcher
     */
    public function setEventDispatcher(EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }    
}
