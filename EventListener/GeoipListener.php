<?php

namespace Raindrop\LocaleBundle\EventListener;

use Raindrop\LocaleBundle\Provider\AllowedLocalesProvider;
use Raindrop\LocaleBundle\Event\FilterLocaleSwitchEvent;
use Raindrop\LocaleBundle\RaindropLocaleBundleEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\EventDispatcher\EventDispatcher;

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
     * @var EventDispatcher
     */
    private $dispatcher;    

    /**
     * Constructor.
     * 
     * @param LoggerInterface         $logger
     * @param AllowedLocalesProvider  $allowedLocales
     * @param RouterInterface         $router
     */
    public function __construct(LoggerInterface $logger, AllowedLocalesProvider $allowedLocales, RouterInterface $router)
    {
        $this->logger = $logger;
		$this->allowedLocales = $allowedLocales;
        $this->router = $router;        
    }    
    
    public function onGeoipLocaleGuess(GetResponseEvent $event)
    {
//        $localeSwitchEvent = new FilterLocaleSwitchEvent($event->getRequest(), 'sq_AF');
//        $this->dispatcher->dispatch(RaindropLocaleBundleEvents::onLocaleChange, $localeSwitchEvent);  
//        
//        $response = new RedirectResponse($this->router->generate('test'), '301');
//        $event->setResponse($response);
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
    
    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            // must be registered after the Router to have access to the _locale and before the Symfony LocaleListener
            KernelEvents::REQUEST => array('onGeoipLocaleGuess')
        );
    }    
}
