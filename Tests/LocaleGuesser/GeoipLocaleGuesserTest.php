<?php

namespace Raindrop\LocaleBundle\Tests\LocaleGuesser;

use Raindrop\LocaleBundle\LocaleGuesser\GeoipLocaleGuesser;
use Raindrop\LocaleBundle\LocaleGuesser\LocaleGuesserInterface;

class GeoipLocaleGuesserTest extends \PHPUnit_Framework_TestCase
{
    public function testGuesserExtendsInterface()
    {
        $metaValidator = $this->getMetaValidatorMock();
        $geoip = $this->getGeoipManagerMock();
        $guesser = $this->getGuesser($metaValidator, $geoip);
        $this->assertTrue($guesser instanceof LocaleGuesserInterface);
    }

    public function testLocaleWithWrongClientIp()
    {
        $metaValidator = $this->getMetaValidatorMock();
        $geoip = $this->getGeoipManagerMock();
        $guesser = $this->getGuesser($metaValidator, $geoip);        
        $request = $this->getRequestMock();
        
        $request->expects($this->once())
                ->method('getClientIp')
                ->will($this->returnValue('127.0.0.1'));
        
        $geoip->expects($this->once())
                ->method('getCountryCode')
                ->will($this->returnValue(''));
        
        $this->assertFalse($guesser->guessLocale($request));
    }    
    
    public function testLocaleWithValidClientIpAndValidCountryCode()
    {
        $metaValidator = $this->getMetaValidatorMock();
        $geoip = $this->getGeoipManagerMock();
        $guesser = $this->getGuesser($metaValidator, $geoip);
        $request = $this->getRequestMock();
        
        $request->expects($this->once())
                ->method('getClientIp')
                ->will($this->returnValue('8.8.8.8'));
        
        $metaValidator->expects($this->once())
                ->method('isValid')
                ->will($this->returnValue(true));        
        
        $geoip->expects($this->once())
                ->method('getCountryCode')
                ->will($this->returnValue('US'));
        
        $this->assertTrue($guesser->guessLocale($request));
    }    
    
    public function testLocaleWithValidClientIpAndWrongCountryCode()
    {
        $metaValidator = $this->getMetaValidatorMock();
        $geoip = $this->getGeoipManagerMock();
        $guesser = $this->getGuesser($metaValidator, $geoip);
        $request = $this->getRequestMock();
        
        $request->expects($this->once())
                ->method('getClientIp')
                ->will($this->returnValue('8.8.8.8'));
        
        $metaValidator->expects($this->once())
                ->method('isValid')
                ->will($this->returnValue(false));        
        
        $geoip->expects($this->once())
                ->method('getCountryCode')
                ->will($this->returnValue('USX'));
        
        $this->assertFalse($guesser->guessLocale($request));
    }    
    
    private function getGuesser($metaValidator, $geoip)
    {
        $guesser = new GeoipLocaleGuesser($metaValidator, $geoip);

        return $guesser;
    }    
    
    private function getMetaValidatorMock()
    {
        $mock = $this->getMockBuilder('\Raindrop\LocaleBundle\Validator\MetaValidator')->disableOriginalConstructor()->getMock();

        return $mock;
    }    
    
    private function getGeoipManagerMock()
    {
        $mock = $this->getMockBuilder('\Raindrop\GeoipBundle\Manager\GeoipManager')->setMethods(array('getCountryCode'))->disableOriginalConstructor()->getMock();

        return $mock;
    }    
    
    private function getRequestMock()
    {
        $mock = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Request')->disableOriginalConstructor()->getMock();

        return $mock;
    }    
}
