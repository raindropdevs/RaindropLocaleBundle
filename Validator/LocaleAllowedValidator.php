<?php

namespace Raindrop\LocaleBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Raindrop\LocaleBundle\Provider\AllowedLocalesProvider;

/**
 * Validator to check if a locale is allowed by the configuration
 *
 */
class LocaleAllowedValidator extends ConstraintValidator
{
    /**
     * @var array
     */
    private $allowedLocales;

    /**
     * @var bool
     */
    private $strictMode;

    /**
     * @var bool
     */
    private $intlExtension;

    /**
     * Constructor
     *
     * @param array $allowedLocales Allowed locales from config
     * @param bool  $strictMode     Match locales strict (e.g. de_DE will not match allowedLocale de)
     * @param bool  $intlExtension  Wether the intl extension is installed
     */
    public function __construct(array $allowedLocales = array(), $strictMode = false, $intlExtension = false)
    {
        $this->allowedLocales = $allowedLocales;
        $this->strictMode = $strictMode;
        $this->intlExtension = $intlExtension;
    }

    /**
     * Validates a Locale
     *
     * @param string     $locale     The locale to be validated
     * @param Constraint $constraint Locale Constraint
     *
     * @throws UnexpectedTypeException
     */
    public function validate($locale, Constraint $constraint)
    {
        if (null === $locale || '' === $locale) {
            return;
        }

        if (!is_scalar($locale) && !(is_object($locale) && method_exists($locale, '__toString'))) {
            throw new UnexpectedTypeException($locale, 'string');
        }

        $locale = (string) $locale;

        if ($this->strictMode) {
            if (!in_array($locale, $this->allowedLocales)) {
                $this->context->addViolation($constraint->message, array('%string%' => $locale));
            }
        } else {
            if ($this->intlExtension) {
                $primary = \Locale::getPrimaryLanguage($locale);
            } else {
                $splittedLocale = explode('_', $locale);
                $primary = count($splittedLocale) > 1 ? $splittedLocale[0] : $locale;
            }

            if (!in_array($locale, $this->allowedLocales) && (!in_array($primary, $this->allowedLocales))) {
                $this->context->addViolation($constraint->message, array('%string%' => $locale));
            }
        }
    }

    /**
     * DI Setter for the AllowedLocalesProvider
     *
     * @param \Raindrop\LocaleBundle\Provider\AllowedLocalesProvider $allowedLocalesProvider
     */
    public function setAllowedLocalesProvider(AllowedLocalesProvider $allowedLocalesProvider)
    {
        $allowedLocales = $allowedLocalesProvider->getAllowedLocales();
        if (!empty ($allowedLocales)) $this->allowedLocales = $allowedLocales;
    }
}
