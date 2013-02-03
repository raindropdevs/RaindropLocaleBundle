<?php

namespace Raindrop\LocaleBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Raindrop\LocaleBundle\LocaleGuesser\LocaleGuesserManager;
use Raindrop\LocaleBundle\Event\FilterLocaleSwitchEvent;
use Raindrop\LocaleBundle\RaindropLocaleBundleEvents;

/**
 * Locale Listener
 *
 */
class LocaleListener implements EventSubscriberInterface
{
    /**
     * @var string Default framework locale
     */
    private $defaultLocale;

    /**
     * @var LocaleGuesserManager
     */
    private $guesserManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    /**
     * Construct the guessermanager
     *
     * @param string               $defaultLocale  Framework default locale
     * @param LocaleGuesserManager $guesserManager Locale Guesser Manager
     * @param LoggerInterface      $logger         Logger
     */
    public function __construct($defaultLocale = 'en', LocaleGuesserManager $guesserManager, LoggerInterface $logger = null)
    {
        $this->defaultLocale = $defaultLocale;
        $this->guesserManager = $guesserManager;
        $this->logger = $logger;
    }

    /**
     * Called at the "kernel.request" event
     *
     * Call the LocaleGuesserManager to guess the locale
     * by the activated guessers
     *
     * Sets the identified locale as default locale to the request
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        /** @var $request \Symfony\Component\HttpFoundation\Request */
        $request = $event->getRequest();

        $request->setDefaultLocale($this->defaultLocale);

        if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST && !$request->isXmlHttpRequest()) {
            $this->logEvent('Request is not a "MASTER_REQUEST" : SKIPPING...');

            return;
        }

        $manager = $this->guesserManager;

        if ($locale = $manager->runLocaleGuessing($request)) {
            $this->logEvent('Setting [ %s ] as defaultLocale for the Request', $locale);
            $request->setLocale($locale);
            if ($manager->getGuesser('session') || $manager->getGuesser('cookie')) {
                $localeSwitchEvent = new FilterLocaleSwitchEvent($request, $locale);
                $this->dispatcher->dispatch(RaindropLocaleBundleEvents::onLocaleChange, $localeSwitchEvent);
            }

            return;
        }
    }

    /**
     * This Listener adds a vary header to all responses.
     *
     * @param FilterResponseEvent $event
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function onLocaleDetectedSetVaryHeader(FilterResponseEvent $event)
    {
        return $event->getResponse()->setVary('Accept-Language');
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
     * Log detection events
     *
     * @param string $logMessage
     * @param string $parameters
     */
    private function logEvent($logMessage, $parameters = null)
    {
        if (null !== $this->logger) {
            $this->logger->info(sprintf($logMessage, $parameters));
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            // must be registered after the Router to have access to the _locale and before the Symfony LocaleListener
            KernelEvents::REQUEST => array(array('onKernelRequest', 24)),
            KernelEvents::RESPONSE => array('onLocaleDetectedSetVaryHeader')
        );
    }
}
