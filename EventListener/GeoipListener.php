<?php

namespace Raindrop\LocaleBundle\EventListener;

use Raindrop\LocaleBundle\Provider\AllowedLocalesProvider;
use Raindrop\LocaleBundle\RaindropLocaleBundleEvents;
use Raindrop\LocaleBundle\Session\LocaleSession;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Session\Session;
use Raindrop\LocaleBundle\Event\FilterLocaleSwitchEvent;

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
     * @var LocaleSession $localesession
     */
    private $localesession;

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
     * @param LocaleSession           $localesession
     * @param Session                 $session
     * @param string                  $sessionVariable
     */
    public function __construct(LoggerInterface $logger, AllowedLocalesProvider $allowedLocales, RouterInterface $router, LocaleSession $localesession, Session $session, $sessionVariable = 'raindrop_locale')
    {
        $this->logger = $logger;
		$this->allowedLocales = $allowedLocales;
        $this->router = $router;
        $this->session = $session;
        $this->sessionVariable = $sessionVariable;
        $this->localesession = $localesession;
    }    
    
    public function onGeoipLocaleGuess(GetResponseEvent $event)
    {
        if (!$this->session->has($this->sessionVariable)) {
            $this->localesession->setLocale('sq_AF');
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
}
