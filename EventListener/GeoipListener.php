<?php

namespace Raindrop\LocaleBundle\EventListener;

use Raindrop\LocaleBundle\Provider\AllowedLocalesProvider;
use Raindrop\LocaleBundle\RaindropLocaleBundleEvents;
use Raindrop\LocaleBundle\Event\FilterLocaleSwitchEvent;
use Raindrop\GeoipBundle\Manager\GeoipManager;
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
     * @var AllowedLocalesProvider $allowedLocalesProvider
     */
    protected $allowedLocalesProvider;

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
     * @var array $include
     */
    protected $include;

    /**
     * @var GeoipManager
     */
    private $geoip;

    /**
     * @var string
     */
    private $chooseCountryRoute;

    /**
     * @var string
     */
    private $internationalCountryCode;

    /**
     * Constructor.
     *
     * @param LoggerInterface $logger
     * @param RouterInterface $router
     * @param Session         $session
     * @param string          $sessionVariable
     * @param array           $include
     */
    public function __construct(LoggerInterface $logger, RouterInterface $router, Session $session, $sessionVariable = 'raindrop_locale', $include = array())
    {
        $this->logger = $logger;
        $this->router = $router;
        $this->session = $session;
        $this->sessionVariable = $sessionVariable;
        $this->include = $include;
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
        $route = $request->attributes->get('_route');

        if (!$this->session->has($this->sessionVariable) && $this->checkInclude($route)) {
            // Get the country code / locale
            $countryCode = $this->geoip->getCountryCode($clientIp);

            if (empty($countryCode)) {
                // ip not recognized
                $locale = ' ';
                $route = $this->router->generate($this->chooseCountryRoute);
            } else {
                $countries = $this->allowedLocalesProvider->getAllowedCountries();

                if (!in_array($countryCode, $countries)) {
                    $international = $this->allowedLocalesProvider->getAllowedInternationalCountries();

                    if (in_array($countryCode, $international)) {
                        // international country
                        $locale = $this->allowedLocalesProvider->getLanguageByInternationalCountry($countryCode);
                    } else {
                        // default international country
                        $locale = 'en';
                    }

                    // international locale
                    $locale .= '_' . $this->internationalCountryCode;
                } else {
                    // country enabled
                    $locale = $this->allowedLocalesProvider->getDefaultLanguageByCountry($countryCode);
                    $locale .= '_' . $countryCode;
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
     * Check if a route must be included in the listener flow
     *
     * @param string $route
     */
    public function checkInclude($route)
    {
        if (empty ($this->include)) {
            return false;
        }

        foreach ($this->include as $include) {
            if (strpos($route, $include) !== false) {
                return true;
            }
        }

        return false;
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

    /**
     * DI Setter for the GeoipManager
     *
     * @param \Raindrop\GeoipBundle\Manager\GeoipManager $geoip
     */
    public function setGeoipManager(GeoipManager $geoip)
    {
        $this->geoip = $geoip;
    }

    /**
     * DI Setter for the AllowedLocalesProvider
     *
     * @param \Raindrop\LocaleBundle\Provider\AllowedLocalesProvider $allowedLocalesProvider
     */
    public function setAllowedLocalesProvider(AllowedLocalesProvider $allowedLocalesProvider)
    {
        $this->allowedLocalesProvider = $allowedLocalesProvider;
    }

    /**
     * DI Setter for the choose_country_route and international_country_code
     *
     * @param string $chooseCountryRoute
     * @param string $internationalCountryCode
     */
    public function setGeoipParameters($chooseCountryRoute, $internationalCountryCode)
    {
        $this->chooseCountryRoute = $chooseCountryRoute;
        $this->internationalCountryCode = $internationalCountryCode;
    }
}
