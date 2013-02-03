<?php

namespace Raindrop\LocaleBundle\Tests\LocaleGuesser;

use Raindrop\LocaleBundle\LocaleGuesser\CookieLocaleGuesser;
use Symfony\Component\HttpFoundation\Request;

class CookieLocaleGuesserTest extends \PHPUnit_Framework_TestCase
{

    public function testLocaleIsRetrievedFromCookieIfSet()
    {
        $request = $this->getRequest();
        $metaValidator = $this->getMetaValidatorMock();

        $metaValidator->expects($this->once())
                ->method('isAllowed')
                ->with('ru')
                ->will($this->returnValue(true));

        $guesser = new CookieLocaleGuesser($metaValidator, 'Raindrop_locale');

        $this->assertTrue($guesser->guessLocale($request));
        $this->assertEquals('ru', $guesser->getIdentifiedLocale());
    }

    public function testLocaleIsNotRetrievedFromCookieIfSetAndInvalid()
    {
        $request = $this->getRequest();
        $metaValidator = $this->getMetaValidatorMock();

        $metaValidator->expects($this->once())
                ->method('isAllowed')
                ->with('ru')
                ->will($this->returnValue(false));

        $guesser = new CookieLocaleGuesser($metaValidator, 'Raindrop_locale');

        $this->assertFalse($guesser->guessLocale($request));
        $this->assertFalse($guesser->getIdentifiedLocale());
    }

    public function testLocaleIsNotRetrievedIfCookieNotSet()
    {
        $request = $this->getRequest(false);
        $metaValidator = $this->getMetaValidatorMock();

        $metaValidator->expects($this->never())
                ->method('isAllowed');

        $guesser = new CookieLocaleGuesser($metaValidator, 'Raindrop_locale');

        $this->assertFalse($guesser->guessLocale($request));
        $this->assertFalse($guesser->getIdentifiedLocale());
    }

    private function getRequest($withLocaleCookie = true)
    {
        $request = Request::create('/');
        if ($withLocaleCookie) {
            $request->cookies->set('Raindrop_locale', 'ru');
        }

        return $request;
    }

    public function getMetaValidatorMock()
    {
        $mock = $this->getMockBuilder('\Raindrop\LocaleBundle\Validator\MetaValidator')->disableOriginalConstructor()->getMock();

        return $mock;
    }
}
