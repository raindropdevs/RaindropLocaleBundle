<?php

namespace Raindrop\LocaleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="CountryRepository")
 * @ORM\Table(name="country")
 */
class Country 
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $name;    

    /**
     * @ORM\Column(type="string")
     * @Assert\MaxLength(
     *     limit=2,
     *     message="Your country code must have at most {{ limit }} characters."
     * )
     */
    protected $code;
    
    /**
     * @ORM\ManyToMany(targetEntity="Language", inversedBy="countries")
     * @ORM\JoinTable(name="countries_languages")
     */
    protected $languages;
    
    /**
     * Constructor.
     */
    public function __construct() 
    {
        $this->languages = new ArrayCollection();
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
     * Set $name
     * 
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }      
    
    /**
     * Set $code
     * 
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }      
}
