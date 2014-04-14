<?php

namespace SensioLabs\JobBoardBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Announcement
 *
 * @ORM\Table()
 * @ORM\Entity()
 */
class Announcement
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title")
     *
     * @Assert\NotBlank(message="Job title should not be empty")
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="company", type="string", length=255)
     *
     * @Assert\NotBlank(message="Company title should not be empty")
     */
    private $company;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=2)
     *
     * @Assert\NotBlank(message="Country title should not be empty")
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=64)
     *
     * @Assert\NotBlank(message="City title should not be empty")
     */
    private $city;

    /**
     * @var integer
     *
     * @ORM\Column(name="contract_type", type="integer")
     *
     * @Assert\NotBlank(message="Contract type should not be empty")
     */
    private $contractType;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     *
     * @Assert\NotBlank(message="Your job offer must be longer")
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="how_to_apply", type="string", length=255, nullable=true)
     */
    private $howToApply;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Announcement
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set company
     *
     * @param string $company
     * @return Announcement
     */
    public function setCompany($company)
    {
        $this->company = $company;
    
        return $this;
    }

    /**
     * Get company
     *
     * @return string 
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set country
     *
     * @param string $country
     * @return Announcement
     */
    public function setCountry($country)
    {
        $this->country = $country;
    
        return $this;
    }

    /**
     * Get country
     *
     * @return string 
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return Announcement
     */
    public function setCity($city)
    {
        $this->city = $city;
    
        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set contractType
     *
     * @param string $contractType
     * @return Announcement
     */
    public function setContractType($contractType)
    {
        $this->contractType = $contractType;
    
        return $this;
    }

    /**
     * Get contractType
     *
     * @return string 
     */
    public function getContractType()
    {
        return $this->contractType;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Announcement
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set howToApply
     *
     * @param string $howToApply
     * @return Announcement
     */
    public function setHowToApply($howToApply)
    {
        $this->howToApply = $howToApply;
    
        return $this;
    }

    /**
     * Get howToApply
     *
     * @return string 
     */
    public function getHowToApply()
    {
        return $this->howToApply;
    }
}
