<?php

namespace Raindrop\LocaleBundle\Tests\LocaleGuesser;

use Raindrop\LocaleBundle\LocaleGuesser\RouterLocaleGuesser;
use Raindrop\LocaleBundle\LocaleGuesser\LocaleGuesserInterface;
use Symfony\Component\HttpFoundation\Request;

class RouterLocaleGuesserTest extends \PHPUnit_Framework_TestCase
{
    public function testGuesserExtendsInterface()
    {
        $guesser = new RouterLocaleGuesser($this->getMetaValidatorMock());
        $this->assertTrue($guesser instanceof LocaleGuesserInterface);
    }

    public function testLocaleIsIdentified()
    {
        $request = $this->getRequestWithLocaleParameter();
        $metaValidator = $this->getMetaValidatorMock();
        $guesser = new RouterLocaleGuesser($metaValidator, false);

        $metaValidator->expects($this->once())
                ->method('isAllowed')
                ->with('en')
                ->will($this->returnValue(true));

        $this->assertTrue($guesser->guessLocale($request));
        $this->assertEquals('en', $guesser->getIdentifiedLocale());
    }

    public function testLocaleIsNotIdentified()
    {
        $request = $this->getRequestWithLocaleQuery('fr');
        $metaValidator = $this->getMetaValidatorMock();
        $guesser = new RouterLocaleGuesser($metaValidator, false);

        $metaValidator->expects($this->never())
                ->method('isAllowed');

        $guesser->guessLocale($request);
        $this->assertEquals(false, $guesser->getIdentifiedLocale());
    }

    private function getRequestWithLocaleParameter($locale = 'en')
    {
        $request = Request::create('/hello-world', 'GET');
        $request->attributes->set('_locale', $locale);

        return $request;
    }

    private function getRequestWithLocaleQuery($locale = 'en')
    {
        $request = Request::create('/hello-world', 'GET');
        $request->query->set('_locale', $locale);

        return $request;
    }

    public function getMetaValidatorMock()
    {
        $mock = $this->getMockBuilder('\Raindrop\LocaleBundle\Validator\MetaValidator')->disableOriginalConstructor()->getMock();

        return $mock;
    }
}
