<?php

namespace Raindrop\LocaleBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\Yaml\Yaml;
use Raindrop\LocaleBundle\Entity\Country;

/**
 * Country Data Fixtures
 */
class LoadCountryData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @{inheritDoc}
     */    
    public function load(ObjectManager $manager)
    {
        $countries = Yaml::parse(__DIR__ . "/../../Resources/data/country.yml");

        foreach ($countries as $code => $name) {
            // persist all countries
            $country = new Country();
            $country->setCode($code);
            $country->setName($name);
            $manager->persist($country);
        }
        
        $manager->flush();
    }

    /**
     * @{inheritDoc}
     */        
    public function getOrder()
    {
        return 1;
    }    
}
