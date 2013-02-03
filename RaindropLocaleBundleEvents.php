<?php

namespace Raindrop\LocaleBundle;

/**
 * Defines aliases for Events in this bundle
 */
final class RaindropLocaleBundleEvents
{
    /**
     * The raindrop_locale.change event is thrown each time the locale changes.
     *
     * The available locales to be chosen can be restricted through the allowed_languages configuration.
     *
     * The event listener receives an Raindrop\LocaleBundle\Event\FilterLocaleSwitchEvent instance
     *
     * @var string
     *
     */
    const onLocaleChange = 'raindrop_locale.change';
}
