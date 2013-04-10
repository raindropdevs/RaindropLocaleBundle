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
                ->leftJoin('c.countries', 'c')
                ->leftJoin('c.language', 'l')
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
                ->leftJoin('c.countries', 'c')
                ->leftJoin('c.language', 'l')
                ->where('c.code = :code')
                ->setParameter('code', $countrycode)
                ->getQuery()
                ->getSingleResult();

        $result = $query->getLanguage()->getCode();

        return $result;
    }
}
