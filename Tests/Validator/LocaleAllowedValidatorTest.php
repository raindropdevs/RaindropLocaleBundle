<?php

namespace Raindrop\LocaleBundle\Tests\Validator;

use Raindrop\LocaleBundle\Validator\LocaleAllowed;
use Raindrop\LocaleBundle\Validator\LocaleAllowedValidator;

/**
 * Test for the LocaleAllowedValidator
 *
 */
class LocaleAllowedValidatorTest extends \PHPUnit_Framework_TestCase
{
    protected $context;

    /**
     * Setup
     */
    public function setUp()
    {
        $this->context = $this->getContext();
    }

    /**
     * Dataprovider for testing each test with and without intl extension
     *
     * @return array
     */
    public function intlExtensionInstalled()
    {
        return array(
            'Extension On' => array(true),
            'Extension Off' => array(false)
        );
    }

    /**
     * @param bool $intlExtension
     *
     * @dataProvider intlExtensionInstalled
     */
    public function testLocaleIsAllowed($intlExtension)
    {
        $constraint = new LocaleAllowed();
        $this->context->expects($this->never())
                ->method('addViolation');
        $this->getLocaleValidator(array('en', 'de'), false, $intlExtension)->validate('en', $constraint);
    }

    /**
     * @param bool $intlExtension
     *
     * @dataProvider intlExtensionInstalled
     */
    public function testLocaleIsAllowedNonStrict($intlExtension)
    {
        $constraint = new LocaleAllowed();
        $this->context->expects($this->never())
                ->method('addViolation');
        $this->getLocaleValidator(array('en', 'de'), false, $intlExtension)->validate('de_DE', $constraint);
    }

    /**
     * @param bool $intlExtension
     *
     * @dataProvider intlExtensionInstalled
     */
    public function testEmptyAllowedList($intlExtension)
    {
        $constraint = new LocaleAllowed();
        $this->context->expects($this->once())
                ->method('addViolation');
        $this->getLocaleValidator(array(), false, $intlExtension)->validate('en', $constraint);
    }

    /**
     * @param bool $intlExtension
     *
     * @dataProvider intlExtensionInstalled
     */
    public function testLocaleIsNotAllowed($intlExtension)
    {
        $locale = 'fr';
        $constraint = new LocaleAllowed();
        $this->context->expects($this->exactly(2))
                ->method('addViolation')
                ->with($this->equalTo($constraint->message), $this->equalTo(array('%string%' => $locale)));
        $this->getLocaleValidator(array('en', 'de'), false, $intlExtension)->validate($locale, $constraint);
        $this->getLocaleValidator(array('en_US', 'de_DE'), false, $intlExtension)->validate($locale, $constraint);
    }

    /**
     * @param bool $intlExtension
     *
     * @dataProvider intlExtensionInstalled
     */
    public function testLocaleIsAllowedStrict($intlExtension)
    {
        $constraint = new LocaleAllowed();
        $this->context->expects($this->never())
                ->method('addViolation');
        $this->getLocaleValidator(array('en', 'de', 'fr'), true, $intlExtension)->validate('fr', $constraint);
        $this->getLocaleValidator(array('de_AT', 'de_CH', 'fr_FR'
        ), true, $intlExtension)->validate('fr_FR', $constraint);
        $this->getLocaleValidator(array('de_AT', 'en', 'fr'), true, $intlExtension)->validate('fr', $constraint);
        $this->getLocaleValidator(array('de_AT', 'en', 'fr'), true, $intlExtension)->validate('de_AT', $constraint);
    }

    /**
     * @param bool $intlExtension
     *
     * @dataProvider intlExtensionInstalled
     */
    public function testLocaleIsNotAllowedStrict($intlExtension)
    {
        $constraint = new LocaleAllowed();
        $this->context->expects($this->exactly(2))
                ->method('addViolation');
        $this->getLocaleValidator(array('en', 'de'), true, $intlExtension)->validate('de_AT', $constraint);
        $this->getLocaleValidator(array('en_US', 'de_DE'), true, $intlExtension)->validate('de', $constraint);
    }

    /**
     * Returns an ExecutionContext Mock
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getContext()
    {
        return $this->getMockBuilder('Symfony\Component\Validator\ExecutionContext')->disableOriginalConstructor()->getMock();
    }

    /**
     * Returns the LocaleAllowedValidator
     *
     * @param array $allowedLocales Array of allowed locales
     * @param bool  $strictMode     Strict Mode
     *
     * @return LocaleAllowedValidator
     */
    private function getLocaleValidator($allowedLocales = array(), $strictMode = false)
    {
        $validator = new LocaleAllowedValidator($allowedLocales, $strictMode);
        $validator->initialize($this->context);

        return $validator;
    }
}
