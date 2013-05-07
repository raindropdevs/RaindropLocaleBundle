<?php

namespace Raindrop\LocaleBundle\Tests\LocaleGuesser;

use Raindrop\LocaleBundle\LocaleGuesser\SonataLocaleGuesser;
use Raindrop\LocaleBundle\LocaleGuesser\LocaleGuesserInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * SonataLocaleGuesserTest
 */
class SonataLocaleGuesserTest extends \PHPUnit_Framework_TestCase
{
    public function testGuesserExtendsInterface()
    {
        $guesser = new SonataLocaleGuesser($this->getMetaValidatorMock());
        $this->assertTrue($guesser instanceof LocaleGuesserInterface);
    }

    public function testSonataRoute()
    {
        $request = $this->getRequestWithSonataAdmin();
        $metaValidator = $this->getMetaValidatorMock();
        $guesser = new SonataLocaleGuesser($metaValidator, false);

        $this->assertTrue($guesser->guessLocale($request));
        $this->assertEquals('fr_FR', $guesser->getIdentifiedLocale());
    }

    public function testSonataRouteWithExludedRoute()
    {
        $request = $this->getRequestWithSonataAdmin('_sonata_admin_route_preview');
        $metaValidator = $this->getMetaValidatorMock();
        $guesser = new SonataLocaleGuesser($metaValidator, false);

        $this->assertTrue($guesser->guessLocale($request));
        $this->assertEquals(null, $guesser->getIdentifiedLocale());
    }

    private function getRequestWithSonataAdmin($sonata = '_sonata_admin_route')
    {
        $request = Request::create('/hello-world', 'GET');
        $request->attributes->set('_sonata_admin', $sonata);
        $request->headers->set('Accept-language', 'fr-FR,fr;q=0.1,en-US;q=0.6,en;q=0.4');

        return $request;
    }

    public function getMetaValidatorMock()
    {
        $mock = $this->getMockBuilder('\Raindrop\LocaleBundle\Validator\MetaValidator')->disableOriginalConstructor()->getMock();

        return $mock;
    }
}
