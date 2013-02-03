<?php

namespace Raindrop\LocaleBundle\LocaleGuesser;

use Symfony\Component\HttpFoundation\Request;

/**
 * Interface for a guesser
 *
 * @author Christophe Willemsen <willemsen.christophe@gmail.com>
 * @author Matthias Breddin <mb@Raindrop.com>
 */
interface LocaleGuesserInterface
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return bool
     */
    public function guessLocale(Request $request);

    /**
     * @return mixed
     */
    public function getIdentifiedLocale();
}
