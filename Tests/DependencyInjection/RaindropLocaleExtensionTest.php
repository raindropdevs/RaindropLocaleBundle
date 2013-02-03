<?php

namespace Raindrop\LocaleBundle\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Raindrop\LocaleBundle\DependencyInjection\RaindropLocaleExtension;
use Symfony\Component\Yaml\Parser;

class RaindropLocaleExtensionTest extends \PHPUnit_Framework_TestCase
{
    protected $configuration;

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testBundleLoadThrowsExceptionUnlessDetectorsOrderIsSet()
    {
        $loader = new RaindropLocaleExtension();
        $config = $this->getEmptyConfig();
        unset($config['guessing_order']);
        $loader->load(array($config), new ContainerBuilder());
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testBundleLoadThrowsExceptionIfNonBooleanValueIsSet()
    {
        $loader = new RaindropLocaleExtension();
        $config = $this->getEmptyConfig();
        $config['router_guesser']['check_query'] = 'hello';
        $loader->load(array($config), new ContainerBuilder());
    }

    /**
     * @return ContainerBuilder
     */
    public function testCreateEmptyConfiguration()
    {
        $this->configuration = new ContainerBuilder();
        $loader = new RaindropLocaleExtension();
        $config = $this->getEmptyConfig();
        $loader->load(array($config), $this->configuration);
        $this->assertTrue($this->configuration instanceof ContainerBuilder);
    }

    /**
     * @return ContainerBuilder
     */
    public function testCreateFullConfiguration()
    {
        $this->configuration = new ContainerBuilder();
        $loader = new RaindropLocaleExtension();
        $config = $this->getFullConfig();
        $loader->load(array($config), $this->configuration);
        $this->assertTrue($this->configuration instanceof ContainerBuilder);
    }

    /**
     * getEmptyConfig
     *
     * @return array
     */
    protected function getEmptyConfig()
    {
        $yaml = <<<EOF
allowed_locales:
    - de
    - fr
    - en
guessing_order:
    - router
    - browser
EOF;
        $parser = new Parser();

        return $parser->parse($yaml);
    }

    protected function getFullConfig()
    {
        $yaml = <<<EOF
allowed_locales:
    - de
    - fr
    - en
guessing_order:
    - router
    - browser
EOF;
        $parser = new Parser();

        return  $parser->parse($yaml);
    }

    protected function tearDown()
    {
        unset($this->configuration);
    }
}
