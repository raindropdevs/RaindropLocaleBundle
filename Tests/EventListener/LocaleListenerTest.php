<?php

namespace Raindrop\LocaleBundle\Tests\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

use Raindrop\LocaleBundle\EventListener\LocaleListener;
use Raindrop\LocaleBundle\LocaleGuesser\LocaleGuesserManager;
use Raindrop\LocaleBundle\LocaleGuesser\RouterLocaleGuesser;
use Raindrop\LocaleBundle\LocaleGuesser\BrowserLocaleGuesser;
use Raindrop\LocaleBundle\LocaleGuesser\CookieLocaleGuesser;
use Raindrop\LocaleBundle\LocaleGuesser\QueryLocaleGuesser;
use Raindrop\LocaleBundle\Validator\MetaValidator;
use Raindrop\LocaleBundle\RaindropLocaleBundleEvents;


class LocaleListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultLocaleWithoutParams()
    {
        $listener = $this->getListener('fr', $this->getGuesserManager());
        $request = Request::create('/');
        $request->headers->set('Accept-language', '');
        $event = $this->getEvent($request);

        $listener->onKernelRequest($event);
        $this->assertEquals('fr', $request->getLocale());
    }

    public function testCustomLocaleIsSetWhenParamsExist()
    {
        $listener = $this->getListener('fr', $this->getGuesserManager());
        $request = Request::create('/', 'GET');
        $request->attributes->set('_locale', 'de');
        $event = $this->getEvent($request);

        $listener->onKernelRequest($event);
        $this->assertEquals('de', $request->getLocale());
    }

    public function testCustomLocaleIsSetWhenQueryExist()
    {
        $listener = $this->getListener('fr', $this->getGuesserManager(array(0 => 'router', 1 => 'query', 2 => 'browser')));
        $request = Request::create('/', 'GET');
        $request->query->set('_locale', 'de');
        $event = $this->getEvent($request);

        $listener->onKernelRequest($event);
        $this->assertEquals('de', $request->getLocale());
    }

    /**
     * Router is prio 1
     * Request contains _locale parameter in router
     * Request contains browser locale preferences
     */
    public function testRouteLocaleIsReturnedIfRouterIsPrio1()
    {
        $request = $this->getFullRequest();
        $manager = $this->getGuesserManager();
        $listener = $this->getListener('en', $manager);
        $event = $this->getEvent($request);
        $listener->onKernelRequest($event);
        $this->assertEquals('es', $request->getLocale());
    }

    /**
     * Browser is prio 1
     * Request contains _locale parameter in router
     * Request contains browser locale preferences
     */
    public function testBrowserLocaleIsReturnedIfBrowserIsPrio1()
    {
        $request = $this->getFullRequest();
        $manager = $this->getGuesserManager(array(1 => 'browser', 2 => 'router'));
        $listener = $this->getListener('en', $manager);
        $event = $this->getEvent($request);
        $listener->onKernelRequest($event);
        $this->assertEquals('fr_FR', $request->getLocale());
    }

    /**
     * Router is prio 1
     * Request DOES NOT contains _locale parameter in router
     * Request contains browser locale preferences
     */
    public function testBrowserTakeOverIfRouterParamsFail()
    {
        $request = $this->getFullRequest(null);
        $manager = $this->getGuesserManager();
        $listener = $this->getListener('en', $manager);
        $event = $this->getEvent($request);
        $listener->onKernelRequest($event);
        $this->assertEquals('fr_FR', $request->getLocale());
    }

    public function testThatGuesserIsNotCalledIfNotInGuessingOrder()
    {
        $request = $this->getRequestWithRouterParam();
        $manager = $this->getGuesserManager(array(0 => 'browser'));
        $listener = $this->getListener('en', $manager);
        $event = $this->getEvent($request);
        $listener->onKernelRequest($event);
        $this->assertEquals('en', $request->getLocale());
    }

    public function testDispatcherIsFired()
    {
        $dispatcherMock = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcher')->disableOriginalConstructor()->getMock();
        $dispatcherMock->expects($this->once())
                        ->method('dispatch')
                        ->with($this->equalTo(RaindropLocaleBundleEvents::onLocaleChange), $this->isInstanceOf('Raindrop\LocaleBundle\Event\FilterLocaleSwitchEvent'));

        $listener = $this->getListener('fr', $this->getGuesserManager());
        $listener->setEventDispatcher($dispatcherMock);


        $event = $this->getEvent($this->getRequestWithRouterParam());
        $listener->onKernelRequest($event);
    }

    public function testDispatcherIsNotFired()
    {
        $dispatcherMock = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcher')->disableOriginalConstructor()->getMock();
        $dispatcherMock->expects($this->never())
                ->method('dispatch');

        $manager = $this->getGuesserManager();
        $manager->removeGuesser('session');
        $manager->removeGuesser('cookie');
        $listener = $this->getListener('fr', $manager);
        $listener->setEventDispatcher($dispatcherMock);

        $event = $this->getEvent($this->getRequestWithRouterParam());
        $listener->onKernelRequest($event);
    }

    /**
     * Request with empty route params and empty browser preferences
     */
    public function testDefaultLocaleIfEmptyRequest()
    {
        $request = $this->getEmptyRequest();
        $manager = $this->getGuesserManager();
        $listener = $this->getListener('en', $manager);
        $event = $this->getEvent($request);
        $listener->onKernelRequest($event);
        $this->assertEquals('en', $request->getLocale());
    }

    public function testAjaxRequestsAreHandled()
    {
        $request = $this->getRequestWithRouterParam('fr');
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        $manager = $this->getGuesserManager(array(0 => 'router'));
        $listener = $this->getListener('en', $manager);
        $event = $this->getEvent($request);
        $listener->onKernelRequest($event);
        $this->assertEquals('fr', $request->getLocale());
    }

    private function getEvent(Request $request)
    {
        return new GetResponseEvent($this->getMock('Symfony\Component\HttpKernel\HttpKernelInterface'), $request, HttpKernelInterface::MASTER_REQUEST);
    }

    private function getListener($locale, $manager)
    {
        $listener = new LocaleListener($locale, $manager);
        $listener->setEventDispatcher(new \Symfony\Component\EventDispatcher\EventDispatcher());
        
        return $listener;
    }

    private function getGuesserManager($order = array(1 => 'router', 2 => 'browser'))
    {
        $allowedLocales = array('de', 'fr', 'fr_FR', 'nl', 'es', 'en');
        $metaValidator = $this->getMetaValidatorMock();
        $callBack = function ($v) use ($allowedLocales) {
            return in_array($v, $allowedLocales);
        };
        $metaValidator->expects($this->any())
                ->method('isAllowed')
                ->will($this->returnCallback($callBack));

        $manager = new LocaleGuesserManager($order);
        $routerGuesser = new RouterLocaleGuesser($metaValidator);
        $browserGuesser = new BrowserLocaleGuesser($metaValidator);
        $cookieGuesser = new CookieLocaleGuesser($metaValidator, 'Raindrop_locale');
        $queryGuesser = new QueryLocaleGuesser($metaValidator, '_locale');
        $manager->addGuesser($queryGuesser, 'query');
        $manager->addGuesser($routerGuesser, 'router');
        $manager->addGuesser($browserGuesser, 'browser');
        $manager->addGuesser($cookieGuesser, 'cookie');

        return $manager;
    }

    /**
     * @return LocaleGuesserInterface
     */
    private function getGuesserMock()
    {
        $mock = $this->getMockBuilder('Raindrop\LocaleBundle\LocaleGuesser\LocaleGuesserInterface')->disableOriginalConstructor()->getMock();

        return $mock;
    }

    /**
     * @return MetaValidator
     */
    private function getMetaValidatorMock()
    {
        $mock = $this->getMockBuilder('\Raindrop\LocaleBundle\Validator\MetaValidator')->disableOriginalConstructor()->getMock();

        return $mock;
    }

    private function getRequestWithRouterParam($routerLocale = 'es')
    {
        $request = Request::create('/');
        $session = new Session(new MockArraySessionStorage());
        $request->setSession($session);
        if (!empty($routerLocale)) {
            $request->attributes->set('_locale', $routerLocale);
        }
        $request->headers->set('Accept-language', '');

        return $request;
    }

    private function getFullRequest($routerLocale = 'es')
    {
        $request = Request::create('/');
        if (!empty($routerLocale)) {
            $request->attributes->set('_locale', $routerLocale);
        }
        $request->headers->set('Accept-language', 'fr-FR,fr;q=0.8,en-US;q=0.6,en;q=0.4');

        return $request;
    }

    private function getEmptyRequest()
    {
        $request = Request::create('/');
        $request->headers->set('Accept-language', '');

        return $request;
    }
}
