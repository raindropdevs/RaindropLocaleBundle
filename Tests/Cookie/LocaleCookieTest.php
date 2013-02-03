<?php

namespace Raindrop\LocaleBundle\Tests\DependencyInjection;

use Raindrop\LocaleBundle\Cookie\LocaleCookie;
use Symfony\Component\HttpFoundation\Cookie;

class LocaleCookieTest extends \PHPUnit_Framework_TestCase
{
    public function testCookieParamsAreSet()
    {
        $localeCookie = new LocaleCookie('raindrop_locale', 86400, '/', null, false, true, true);
        $cookie = $localeCookie->getLocaleCookie('en');
        $this->assertTrue($cookie instanceof Cookie);
        $this->assertEquals('raindrop_locale', $cookie->getName());
        $this->assertEquals('en', $cookie->getValue());
        $this->assertEquals('/', $cookie->getPath());
        $this->assertEquals(null, $cookie->getDomain());
        $this->assertTrue($cookie->isHttpOnly());
        $this->assertFalse($cookie->isSecure());
    }

    public function testCookieExpiresDateTime()
    {
        $localeCookie = new LocaleCookie('raindrop_locale', 86400, '/', null, false, true, true);
        $cookie = $localeCookie->getLocaleCookie('en');
        $this->assertTrue($cookie->getExpiresTime() > time());
        $this->assertTrue($cookie->getExpiresTime() <= (time() + 86400));
    }
}
