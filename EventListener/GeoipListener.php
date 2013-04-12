<?php

namespace Raindrop\LocaleBundle\EventListener;

use Raindrop\LocaleBundle\Provider\AllowedLocalesProvider;
use Raindrop\LocaleBundle\RaindropLocaleBundleEvents;
use Raindrop\LocaleBundle\Event\FilterLocaleSwitchEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Session\Session;
use Raindrop\GeoipBundle\Manager\GeoipManager;

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
     * @var GeoipManager
     */
    private $geoip;

    /**
     * Constructor.
     *
     * @param LoggerInterface        $logger
     * @param AllowedLocalesProvider $allowedLocales
     * @param RouterInterface        $router
     * @param Session                $session
     * @param string                 $sessionVariable
     * @param GeoipManager           $geoip
     */
    public function __construct(LoggerInterface $logger, AllowedLocalesProvider $allowedLocales, RouterInterface $router,  GeoipManager $geoip, Session $session, $sessionVariable = 'raindrop_locale', $chooseCountryRoute, $internationalCountryCode)
    {
        $this->logger = $logger;
        $this->allowedLocales = $allowedLocales;
        $this->router = $router;
        $this->geoip = $geoip;
        $this->session = $session;
        $this->sessionVariable = $sessionVariable;
        $this->chooseCountryRoute = $chooseCountryRoute;
        $this->internationalCountryCode = $internationalCountryCode;
    }

    /**
     * Check the client ip and redirect the user to the correct route.
     * It also sets the right locale
     *
     * @param GetResponseEvent $event
     */
    public function onGeoipLocaleGuess(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $clientIp = $request->getClientIp();

        if (!$this->session->has($this->sessionVariable)) {
            // Get the country code / locale
            $countryCode = $this->geoip->getCountryCode($clientIp);

            if (empty($countryCode)) {
                // ip not recognized
                $locale = ' ';
                $route = $this->router->generate($this->chooseCountryRoute);
            } else {
                $countries = $this->allowedLocales->getAllowedCountries();

                if (!in_array($countryCode, $countries)) {
                    $international = $this->allowedLocales->getAllowedInternationalCountries();

                    if (in_array($countryCode, $international)) {
                        // international country
                        $locale = $this->allowedLocales->getLanguageByInternationalCountry($countryCode);
                    } else {
                        // default international country
                        $locale = 'en';
                    }

                    // international locale
                    $locale .= '_' . $this->internationalCountryCode;
                } else {
                    // country enabled
                    $locale = $this->allowedLocales->getDefaultLanguageByCountry($countryCode);
                }
                // home page whit locale
                $route = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBaseUrl() . '/' . $locale;
            }

            // dispatch FilterLocaleSwitchEvent
            $localeSwitchEvent = new FilterLocaleSwitchEvent($request, $locale);
            $this->dispatcher->dispatch(RaindropLocaleBundleEvents::onLocaleChange, $localeSwitchEvent);

            // redirect to given route
            $response = new RedirectResponse($route);
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
