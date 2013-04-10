<?php

namespace Raindrop\LocaleBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Country Repository
 */
class CountryRepository extends EntityRepository
{
    /**
     * Find allowed locales (language_COUNTRY)
     *
     * @return array
     */
    public function findAllowedLocales()
    {
        $query = $this->createQueryBuilder('c')
                ->select('c', 'dl', 'l')
                ->leftJoin('c.defaultLanguage', 'dl')
                ->leftJoin('c.languages', 'l')
                ->where('c.enabled = true')
                ->getQuery()
                ->getResult();

        $result = array();

        foreach ($query as $country) {
            $result[] = $country->getDefaultLanguage()->getCode().'_'.$country->getCode();
            foreach ($country->getLanguages() as $language) {
                $result[] = $language->getCode().'_'.$country->getCode();
            }
        }

        return $result;
    }

    /**
     * Find the allowed countries
     *
     * @return array
     */
    public function findAllowedCountries()
    {
        $query = $this->createQueryBuilder('c')
                ->select('c')
                ->where('c.enabled = true')
                ->getQuery()
                ->getResult();

        $result = array();

        foreach ($query as $country) {
            $result[] = $country->getCode();
        }

        return $result;
    }

    /**
     * Find the default language of a country
     *
     * @param  string $countryCode
     * @return string
     */
    public function findDefaultLanguageByCountryCode($countryCode)
    {
        $query = $this->createQueryBuilder('c')
                ->select('c', 'dl')
                ->leftJoin('c.defaultLanguage', 'dl')
                ->where('c.code = :code')
                ->setParameter('code', $countryCode)
                ->getQuery()
                ->getSingleResult();

        $result = $query->getDefaultLanguage()->getCode();

        return $result;
    }
}
