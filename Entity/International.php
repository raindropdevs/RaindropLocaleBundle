<?php

namespace Raindrop\LocaleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Raindrop\LocaleBundle\Entity\Country;

/**
 * @ORM\Entity(repositoryClass="InternationalRepository")
 * @ORM\Table(name="international")
 */
class International
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="Country")
     */
    protected $countries;

    /**
     * @ORM\ManyToOne(targetEntity="Language")
     */
    protected $language;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->countries = new ArrayCollection();
    }

    /**
     * Returns the country unique id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCountries()
    {
        return $this->countries;
    }

    /**
     * Set $language
     *
     * @param Language $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf("International - %s", $this->getLanguage());
    }

    /**
     * Add countries
     *
     * @param  \Raindrop\LocaleBundle\Entity\Country $countries
     * @return International
     */
    public function addCountrie(\Raindrop\LocaleBundle\Entity\Country $countries)
    {
        $this->countries[] = $countries;

        return $this;
    }

    /**
     * Remove countries
     *
     * @param \Raindrop\LocaleBundle\Entity\Country $countries
     */
    public function removeCountrie(\Raindrop\LocaleBundle\Entity\Country $countries)
    {
        $this->countries->removeElement($countries);
    }
}
