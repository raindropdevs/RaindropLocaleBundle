<?php

namespace Raindrop\LocaleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="LanguageRepository")
 * @ORM\Table(name="language")
 */
class Language 
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
     *     message="Your language code must have at most {{ limit }} characters."
     * )
     */
    protected $code;
    
    /**
     * @ORM\ManyToMany(targetEntity="Country", mappedBy="languages")
     */
    protected $countries;
    
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

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }
}
