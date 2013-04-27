<?php

namespace Raindrop\LocaleBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\Yaml\Yaml;
use Raindrop\LocaleBundle\Entity\Language;

/**
 * Language Data Fixtures
 */
class LoadLanguageData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @{inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $languages = Yaml::parse(__DIR__ . "/../../Resources/data/language.yml");

        foreach ($languages as $code => $name) {
            // persist all countries
            $language = new Language();
            $language->setCode($code);
            $language->setName($name);
            $manager->persist($language);
        }

        $manager->flush();
    }

    /**
     * @{inheritDoc}
     */
    public function getOrder()
    {
        return 2;
    }
}
