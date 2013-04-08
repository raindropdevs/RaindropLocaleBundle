<?php

namespace Raindrop\LocaleBundle\EventListener;

use Raindrop\LocaleBundle\Provider\AllowedLocalesProvider;
use Raindrop\LocaleBundle\RaindropLocaleBundleEvents;
use Raindrop\LocaleBundle\Session\LocaleSession;
use Raindrop\LocaleBundle\Event\FilterLocaleSwitchEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * GeoipListener
 */
class GeoipListener implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface $logger 
     */
    protected $logger;
    
	/**
     * @var AllowedLocalesProvider $allowedLocales
     */
    protected $allowedLocales;

    /**
     * @var RouterInterface $router
     */
    protected $router;

    /**
     * @var Session $session
     */
    protected $session;

    /**
     * @var string $sessionVariable
     */
    protected $sessionVariable;

    /**
     * Constructor.
     * 
     * @param LoggerInterface         $logger
     * @param AllowedLocalesProvider  $allowedLocales
     * @param RouterInterface         $router
     * @param Session                 $session
     * @param string                  $sessionVariable
     */
    public function __construct(LoggerInterface $logger, AllowedLocalesProvider $allowedLocales, RouterInterface $router, Session $session, $sessionVariable = 'raindrop_locale')
    {
        $this->logger = $logger;
		$this->allowedLocales = $allowedLocales;
        $this->router = $router;
        $this->session = $session;
        $this->sessionVariable = $sessionVariable;
    }    
    
    public function onGeoipLocaleGuess(GetResponseEvent $event)
    {
        if (!$this->session->has($this->sessionVariable)) {
            $localeSwitchEvent = new FilterLocaleSwitchEvent($event->getRequest(), 'sq_AF');
            $this->dispatcher->dispatch(RaindropLocaleBundleEvents::onLocaleChange, $localeSwitchEvent);            
            
            $response = new RedirectResponse($this->router->generate('_demo'), '301');
            $event->setResponse($response);
        }
    }
    
    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            // must be registered after the Router to have access to the _locale and before the Symfony LocaleListener
            KernelEvents::REQUEST => array(array('onGeoipLocaleGuess', 24))
        );
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
