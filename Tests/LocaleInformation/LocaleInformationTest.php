<?php

namespace Raindrop\LocaleBundle\Tests\LocaleInformation;

use Raindrop\LocaleBundle\Tests\Validator\BaseMetaValidator;
use Raindrop\LocaleBundle\LocaleInformation\LocaleInformation;

/**
 * Test for the LocaleInformation
 *
 */
class LocaleInformationTest extends BaseMetaValidator
{
    protected $allowedLocales = array('en', 'de', 'fr_CH');

    public function testGetAllowedLocalesFromConfiguration()
    {
        $metaValidator = $this->getMetaValidator($this->allowedLocales);
        $information = new LocaleInformation($metaValidator, $this->allowedLocales);
        $this->assertSame($this->allowedLocales, $information->getAllowedLocalesFromConfiguration());
    }

    /**
     * @param bool $intlExtension
     *
     * @dataProvider intlExtensionInstalled
     */
    public function testGetAllAllowedLocales($intlExtension)
    {
        $metaValidator = $this->getMetaValidator($this->allowedLocales, $intlExtension);
        $information = new LocaleInformation($metaValidator);
        $foundLocales = $information->getAllAllowedLocales();

        $this->assertContains('en_GB', $foundLocales);
        $this->assertContains('en_US', $foundLocales);
        $this->assertContains('de_CH', $foundLocales);
        $this->assertContains('de_AT', $foundLocales);
        $this->assertContains('fr_CH', $foundLocales);
        $this->assertContains('de', $foundLocales);
        $this->assertContains('en', $foundLocales);
    }

    /**
     * @param bool $intlExtension
     *
     * @dataProvider intlExtensionInstalled
     */
    public function testGetAllAllowedLocalesStrict($intlExtension)
    {
        $metaValidator = $this->getMetaValidator($this->allowedLocales, $intlExtension, true);
        $information = new LocaleInformation($metaValidator);
        $foundLocales = $information->getAllAllowedLocales();
        $this->assertNotContains('en_US', $foundLocales);
        $this->assertNotContains('de_AT', $foundLocales);
        $this->assertContains('de', $foundLocales);
        $this->assertContains('en', $foundLocales);
        $this->assertContains('fr_CH', $foundLocales);
    }

    /**
     * @param bool $intlExtension
     *
     * @dataProvider intlExtensionInstalled
     */
    public function testGetAllAllowedLocalesLanguageIdenticalToRegion($intlExtension)
    {
        $this->markTestSkipped('symfony/locale is buggy');
        $metaValidator = $this->getMetaValidator($this->allowedLocales, $intlExtension);
        $information = new LocaleInformation($metaValidator);
        $foundLocales = $information->getAllAllowedLocales();
        $this->assertContains('de_DE', $foundLocales);
        $this->assertContains('fr_FR', $foundLocales);
    }

    /**
     * @param bool $intlExtension
     *
     * @dataProvider intlExtensionInstalled
     */
    public function testGetAllAllowedLanguages($intlExtension)
    {
        $metaValidator = $this->getMetaValidator($this->allowedLocales, $intlExtension);
        $information = new LocaleInformation($metaValidator);
        $foundLanguages = $information->getAllAllowedLanguages();
        $this->assertContains('de_CH', $foundLanguages);
        $this->assertNotContains('de_LI', $foundLanguages);
    }

    /**
     * @param bool $intlExtension
     *
     * @dataProvider intlExtensionInstalled
     */
    public function testGetAllAllowedLanguagesStrict($intlExtension)
    {
        $metaValidator = $this->getMetaValidator($this->allowedLocales, $intlExtension, true);
        $information = new LocaleInformation($metaValidator);
        $foundLanguages = $information->getAllAllowedLanguages();
        $this->assertCount(count($this->allowedLocales), $foundLanguages);
        foreach ($foundLanguages as $locale) {
            $this->assertContains($locale, $this->allowedLocales);
        }
    }
}