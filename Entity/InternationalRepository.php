<?php

namespace Raindrop\LocaleBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * International Repository
 */
class InternationalRepository extends EntityRepository
{
    /**
     * Find the allowed international countries
     *
     * @return array
     */
    public function findAllowedInternationalCountries()
    {
        $query = $this->createQueryBuilder('i')
                ->select('i', 'c', 'l')
                ->leftJoin('i.countries', 'c')
                ->leftJoin('i.language', 'l')
                ->getQuery()
                ->getResult();

        $result = array();

        foreach ($query as $international) {
            foreach ($international->getCountries() as $country) {
                $result[] = $country->getCode();
            }
        }

        return $result;
    }

    /**
     * Find the language of a international country
     *
     * @param  string $countrycode
     * @return string
     */
    public function findLanguageByInternationalCountry($countrycode)
    {
        $query = $this->createQueryBuilder('i')
                ->select('i', 'c', 'l')
                ->leftJoin('i.countries', 'c')
                ->leftJoin('i.language', 'l')
                ->where('c.code = :code')
                ->setParameter('code', $countrycode)
                ->getQuery()
                ->getSingleResult();

        $result = $query->getLanguage()->getCode();

        return $result;
    }

    /**
     * Find allowed locales (language_COUNTRY)
     *
     * @param  string $internationalCountryCode
     * @return array
     */
    public function findAllowedLocales($internationalCountryCode = 'JJ')
    {
        $query = $this->createQueryBuilder('i')
                ->select('i', 'l')
                ->leftJoin('i.language', 'l')
                ->getQuery()
                ->getResult();

        $result = array();

        foreach ($query as $country) {
            $result[] = $country->getLanguage()->getCode() . '_' . $internationalCountryCode;
        }

        return $result;
    }

    /**
     * Find international countries
     *
     * @param  string $internationalCountryCode
     * @return array
     */
    public function findCountryList($internationalCountryCode = 'JJ')
    {
        $query = $this->createQueryBuilder('i')
                ->select('i', 'l')
                ->leftJoin('i.language', 'l')
                ->getQuery()
                ->getResult();

        $result = array();

        foreach ($query as $country) {
            $result[$internationalCountryCode . strtoupper($country->getLanguage()->getCode())] = $country->getLanguage()->getCode();
        }

        return $result;
    }

    /**
     * Find the allowed countries
     *
     * @param  string $internationalCountryCode
     * @return array
     */
    public function findAllowedCountries($internationalCountryCode = 'JJ')
    {
        $query = $this->createQueryBuilder('i')
                ->select('i', 'l')
                ->leftJoin('i.language', 'l')
                ->getQuery()
                ->getResult();

        $result = array();

        foreach ($query as $country) {
            $result[] = $internationalCountryCode . strtoupper($country->getLanguage()->getCode());
        }

        return $result;
    }
}
