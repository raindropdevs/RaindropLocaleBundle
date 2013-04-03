<?php

namespace Raindrop\LocaleBundle\Tests\EventListener;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\Response;

use Raindrop\LocaleBundle\Event\FilterLocaleSwitchEvent;
use Raindrop\LocaleBundle\EventListener\LocaleUpdateListener;
use Raindrop\LocaleBundle\Session\LocaleSession;
use Raindrop\LocaleBundle\Cookie\LocaleCookie;

class LocaleUpdateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EventDispatcher
     */
    private $dispatcher;
    /**
     * @var LocaleSession
     */
    private $session;

    public function setUp()
    {
        $this->dispatcher = new EventDispatcher();
        $this->session = new LocaleSession(new Session(new MockArraySessionStorage()));
    }

    public function testCookieIsNotUpdatedNoGuesser()
    {
        $request = $this->getRequest(false);
        $listener = $this->getLocaleUpdateListener(array('session'), true);

        $this->assertFalse($listener->updateCookie($request, true));
        $this->assertFalse($listener->updateCookie($request, false));

        $listener->onLocaleChange($this->getFilterLocaleSwitchEvent(false));
        $addedListeners = $this->dispatcher->getListeners(KernelEvents::RESPONSE);

        $this->assertSame(array(), $addedListeners);
    }

    public function testCookieIsNotUpdatedOnSameLocale()
    {
        $listener = $this->getLocaleUpdateListener(array('cookie'), true);
        $listener->onLocaleChange($this->getFilterLocaleSwitchEvent(true, 'de'));
        $addedListeners = $this->dispatcher->getListeners(KernelEvents::RESPONSE);
        $this->assertSame(array(), $addedListeners);
    }


    public function testCookieIsUpdatedOnChange()
    {
        $listener = $this->getLocaleUpdateListener(array('cookie'), true);
        $listener->onLocaleChange($this->getFilterLocaleSwitchEvent(false));
        $addedListeners = $this->dispatcher->getListeners(KernelEvents::RESPONSE);
        $this->assertContains('updateCookieOnResponse', $addedListeners[0]);
    }

    public function testCookieIsNotUpdatedWithFalseSetCookieOnChange()
    {
        $listener = $this->getLocaleUpdateListener(array('cookie'), false);
        $listener->onLocaleChange($this->getFilterLocaleSwitchEvent(false));
        $addedListeners = $this->dispatcher->getListeners(KernelEvents::RESPONSE);
        $this->assertSame(array(), $addedListeners);
    }

    public function testUpdateCookieOnResponse()
    {
        $event = $this->getEvent($this->getRequest());
        $listener = $this->getLocaleUpdateListener();

        $reflectionClass = new \ReflectionClass($listener);
        $property = $reflectionClass->getProperty('locale');
        $property->setAccessible(true);
        $property->setValue($listener, 'es');

        $response = $listener->updateCookieOnResponse($event);

        /** @var $cookie \Symfony\Component\HttpFoundation\Cookie */
        list($cookie) = $response->headers->getCookies();
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Cookie', $cookie);
        $this->assertEquals('raindrop_locale', $cookie->getName());
        $this->assertEquals('es', $cookie->getValue());

    }

    public function testUpdateSession()
    {
        $this->session->setLocale('el');
        $listener = $this->getLocaleUpdateListener(array('session'));

        $reflectionClass = new \ReflectionClass($listener);
        $property = $reflectionClass->getProperty('locale');
        $property->setAccessible(true);
        $property->setValue($listener, 'tr');

        $this->assertTrue($listener->updateSession());
    }

    public function testNotUpdateSessionNoGuesser()
    {
        $this->session->setLocale('el');
        $listener = $this->getLocaleUpdateListener(array('cookie'));

        $reflectionClass = new \ReflectionClass($listener);
        $property = $reflectionClass->getProperty('locale');
        $property->setAccessible(true);
        $property->setValue($listener, 'el');

        $this->assertFalse($listener->updateSession());
    }

    public function testNotUpdateSessionSameLocale()
    {
        $this->session->setLocale('el');
        $listener = $this->getLocaleUpdateListener(array('session'));

        $reflectionClass = new \ReflectionClass($listener);
        $property = $reflectionClass->getProperty('locale');
        $property->setAccessible(true);
        $property->setValue($listener, 'el');

        $this->assertFalse($listener->updateSession());
    }

    private function getFilterLocaleSwitchEvent($withCookieSet = true, $locale = 'fr')
    {
        return new FilterLocaleSwitchEvent($this->getRequest($withCookieSet), $locale);
    }

    private function getLocaleUpdateListener($registeredGuessers = array(), $updateCookie = false)
    {
        $listener = new LocaleUpdateListener($this->getLocaleCookie($updateCookie),
            $this->session,
            $this->dispatcher,
            $registeredGuessers);

        return $listener;
    }

    private function getEvent(Request $request)
    {
        return new FilterResponseEvent($this->getMock('Symfony\Component\HttpKernel\HttpKernelInterface'), $request, HttpKernelInterface::MASTER_REQUEST, new Response);
    }


    private function getLocaleCookie($updateCookie)
    {
        $cookie = new LocaleCookie('raindrop_locale', 86400, '/', null, false, true, $updateCookie);

        return $cookie;
    }

    private function getRequest($withCookieSet = false)
    {
        $request = Request::create('/', 'GET', array(), $withCookieSet ? array('raindrop_locale' => 'de') : array());

        return $request;
    }
}
