<?php

namespace Raindrop\LocaleBundle\LocaleGuesser;

use Symfony\Component\HttpFoundation\Request;
use Raindrop\LocaleBundle\Validator\MetaValidator;
use Raindrop\GeoipBundle\Manager\GeoipManager;

/**
 * Description of GeoipLocaleGuesser
 *
 * @author edoardo
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

        $clientIp = $request->getClientIp();
        // Get the country code / locale
        $countryCode = strtolower($this->geoip->getCountryCode($clientIp));

        if (empty($countryCode)) {
            return false;
        }        
        
        // If the locale is allowed, return the locale.
        if ($validator->isAllowed($countryCode)) {
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
}
