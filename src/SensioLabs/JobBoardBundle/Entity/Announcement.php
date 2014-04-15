<?php

namespace SensioLabs\JobBoardBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Intl\Intl;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Sluggable\Util as Sluggable;
use SensioLabs\JobBoardBundle\Exception\InvalidStatusUpdateException;

/**
 * Announcement
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="SensioLabs\JobBoardBundle\Entity\AnnouncementRepository")
 */
class Announcement
{
    const FULL_TIME = 'Full Time';
    const PART_TIME = 'Part Time';
    const INTERNSHIP = 'Internship';
    const FREELANCE = 'Freelance';
    const ALTERNANCE = 'Alternance';
    const STATUS_SAVED = 'Saved';

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
     * @Gedmo\Slug(fields={"title"})
     *
     * @ORM\Column(name="title_slug", length=128, unique=true)
     */
    private $titleSlug;

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
     * @var string
     *
     * @ORM\Column(name="contract_type", type="string", length=16)
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
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=16)
     */
    private $status;

    /**
     * @var datetime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

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

    /**
     * Get titleSlug
     *
     * @return string
     */
    public function getTitleSlug()
    {
        return $this->titleSlug;
    }

    public function getContractTypeSlug()
    {
        return Sluggable\Urlizer::urlize($this->getContractType(), '-');
    }

    public function getCountryName()
    {
        return Intl::getRegionBundle()->getCountryName($this->country);
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Announcement
     */
    public function setStatus($status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    public function isSaved()
    {
        if ($this->status === self::STATUS_SAVED)
        {
            return true;
        }

        return false;
    }

    /**
     * Set the status to saved
     *
     * @throws InvalidStatusUpdateException
     */
    public function backup()
    {
        if ($this->isSaved()) {
            return;
        }

        if ($this->status === null) {
            $this->status = self::STATUS_SAVED;

            return;
        }

        throw new InvalidStatusUpdateException(sprintf('Entity status cannot pass from %s to %s', $this->status, self::STATUS_SAVED));
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}
