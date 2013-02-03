<?php

namespace Raindrop\LocaleBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * LocaleAllowed Constraint
 *
 * @Annotation
 */
class LocaleAllowed extends Constraint
{
    /**
     * @var string
     */
    public $message = 'The locale "%string%" is not allowed by application configuration.';

    /**
     * {@inheritDoc}
     */
    public function validatedBy()
    {
        return 'raindrop_locale.validator.locale_allowed';
    }
}