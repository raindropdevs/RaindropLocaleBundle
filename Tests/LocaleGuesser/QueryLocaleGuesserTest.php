<?php

namespace Raindrop\LocaleBundle\Tests\LocaleGuesser;

use Raindrop\LocaleBundle\LocaleGuesser\QueryLocaleGuesser;
use Raindrop\LocaleBundle\LocaleGuesser\LocaleGuesserInterface;
use Symfony\Component\HttpFoundation\Request;

class QueryLocaleGuesserTest extends \PHPUnit_Framework_TestCase
{
    public function testGuesserExtendsInterface()
    {
        $guesser = new QueryLocaleGuesser($this->getMetaValidatorMock());
        $this->assertTrue($guesser instanceof LocaleGuesserInterface);
    }

    public function testLocaleIsIdentifiedFromRequestQuery()
    {
        $request = $this->getRequestWithLocaleQuery();
        $metaValidator = $this->getMetaValidatorMock();
        $guesser = new QueryLocaleGuesser($metaValidator);

        $metaValidator->expects($this->once())
                ->method('isAllowed')
                ->with('en')
                ->will($this->returnValue(true));

        $this->assertTrue($guesser->guessLocale($request));
        $this->assertEquals('en', $guesser->getIdentifiedLocale());
    }

    public function testLocaleIsNotIdentifiedFromRequestQuery()
    {
        $request = $this->getRequestWithLocaleQuery();
        $metaValidator = $this->getMetaValidatorMock();
        $guesser = new QueryLocaleGuesser($metaValidator);

        $metaValidator->expects($this->once())
                ->method('isAllowed')
                ->with('en')
                ->will($this->returnValue(false));

        $this->assertFalse($guesser->guessLocale($request));
        $this->assertFalse($guesser->getIdentifiedLocale());
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
