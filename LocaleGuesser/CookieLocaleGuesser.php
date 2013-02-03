<?php

namespace Raindrop\LocaleBundle\LocaleGuesser;

use Symfony\Component\HttpFoundation\Request;
use Raindrop\LocaleBundle\Validator\MetaValidator;

/**
 * Cookie Guesser for retrieving a previously detected locale from a cookie
 *
 * @author Matthias Breddin <mb@Raindrop.com>
 * @author Christophe Willemsen <willemsen.christophe@gmail.com>
 */
class CookieLocaleGuesser implements LocaleGuesserInterface
{
    /**
     * @var string
     */
    private $identifiedLocale;

    /**
     * @var string
     */
    private $localeCookieName;

    /**
     * @var MetaValidator
     */
    private $metaValidator;

    /**
     * Constructor
     *
     * @param MetaValidator $metaValidator    MetaValidator
     * @param string        $localeCookieName Name of the cookie var
     */
    public function __construct(MetaValidator $metaValidator, $localeCookieName)
    {
        $this->metaValidator = $metaValidator;
        $this->localeCookieName = $localeCookieName;
    }

    /**
     * Retrieve from cookie
     *
     * @param Request $request Request
     *
     * @return bool
     */
    public function guessLocale(Request $request)
    {
        if ($request->cookies->has($this->localeCookieName) && $this->metaValidator->isAllowed($request->cookies->get($this->localeCookieName))) {
            $this->identifiedLocale = $request->cookies->get($this->localeCookieName);

            return true;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getIdentifiedLocale()
    {
        if (null === $this->identifiedLocale) {
            return false;
        }

        return $this->identifiedLocale;
    }
}
