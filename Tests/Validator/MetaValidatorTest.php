<?php

namespace Raindrop\LocaleBundle\Tests\Validator;

/**
 * Test for the LocaleValidator
 *
 */
class MetaValidatorTest extends BaseMetaValidator
{
    /**
     * @param bool $intlExtension
     *
     * @dataProvider intlExtensionInstalled
     */
    public function testLocaleIsAllowedNonStrict($intlExtension)
    {
        $metaValidator = $this->getMetaValidator(array('en', 'de'), $intlExtension);
        $this->assertTrue($metaValidator->isAllowed('en'));
        $this->assertTrue($metaValidator->isAllowed('en_US'));
        $this->assertTrue($metaValidator->isAllowed('de'));
        $this->assertTrue($metaValidator->isAllowed('de_AT'));
        $this->assertTrue($metaValidator->isAllowed('de_FR'));
    }

    /**
     * @param bool $intlExtension
     *
     * @dataProvider intlExtensionInstalled
     */
    public function testLocaleIsNotAllowedNonStrict($intlExtension)
    {
        $metaValidator = $this->getMetaValidator(array('en', 'de'), $intlExtension);
        $this->assertFalse($metaValidator->isAllowed('fr'));
        $this->assertFalse($metaValidator->isAllowed('fr_FR'));
    }

    /**
     * @param bool $intlExtension
     *
     * @dataProvider intlExtensionInstalled
     */
    public function testLocaleIsAllowedStrict($intlExtension)
    {
        $metaValidator = $this->getMetaValidator(array('en', 'de_AT'), $intlExtension, true);
        $this->assertTrue($metaValidator->isAllowed('en'));
        $this->assertTrue($metaValidator->isAllowed('de_AT'));
    }

    /**
     * @param bool $intlExtension
     *
     * @dataProvider intlExtensionInstalled
     */
    public function testLocaleIsNotAllowedStrict($intlExtension)
    {
        $metaValidator = $this->getMetaValidator(array('en', 'de_AT'), $intlExtension, true);
        $this->assertfalse($metaValidator->isAllowed('en_US'));
        $this->assertfalse($metaValidator->isAllowed('de'));
    }
}
