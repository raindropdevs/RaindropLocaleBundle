<?php

namespace Raindrop\LocaleBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Locale Constraint
 *
 * @Annotation
 */
class Locale extends Constraint
{
    /**
     * @var string
     */
    public $message = 'The locale "%string%" is not a valid locale';

    /**
     * {@inheritDoc}
     */
    public function validatedBy()
    {
        return 'raindrop_locale.validator.locale';
    }
}
