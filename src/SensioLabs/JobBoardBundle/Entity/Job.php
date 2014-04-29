<?php

namespace SensioLabs\JobBoardBundle\Entity;

use SensioLabs\JobBoardBundle\Entity\User;
use SensioLabs\JobBoardBundle\Entity\Location;
use SensioLabs\JobBoardBundle\Entity\Company;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Eko\FeedBundle\Item\Writer\ItemInterface;

/**
 * Job
 *
 * @ORM\Table(name="sl_job")
 * @ORM\Entity(repositoryClass="SensioLabs\JobBoardBundle\Repository\JobRepository")
 */
class Job implements ItemInterface
{
    const FULL_TIME = 'Full Time';
    const PART_TIME = 'Part Time';
    const INTERNSHIP = 'Internship';
    const FREELANCE = 'Freelance';
    const ALTERNANCE = 'Alternance';

    const STATUS_NEW = 'New';
    const STATUS_ORDERED = 'Ordered';
    const STATUS_PUBLISHED = 'Published';
    const STATUS_EXPIRED = 'Expired';
    const STATUS_ARCHIVED = 'Archived';
    const STATUS_DELETED = 'Deleted';
    const STATUS_RESTORED = 'Restored';

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
     * @Assert\NotBlank(message="job.title.not_blank")
     */
    private $title;

    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"title"})
     *
     * @ORM\Column(name="slug", length=128, unique=true)
     */
    private $slug;

    /**

     * @ORM\ManyToOne(targetEntity="Company", cascade={"persist"}, inversedBy="jobs")
     */
    private $firm;

    /**
     * @var string
     *
     * @ORM\Column(name="contract_type", type="string", length=16)
     *
     * @Assert\NotBlank(message="job.contract_type.not_blank")
     */
    private $contractType;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     *
     * @Assert\NotBlank(message="job.description.not_blank")
     * @Assert\Length(min = "20", minMessage = "job.description.length")
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
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var datetime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    /**
     * @var datetime
     *
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     */
    private $deletedAt;

    /**
     * @var datetime
     *
     * @ORM\Column(name="visible_from", type="datetime", nullable=true)
     */
    private $visibleFrom;

    /**
     * @var datetime
     *
     * @ORM\Column(name="visible_to", type="datetime", nullable=true)
     */
    private $visibleTo;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="jobs")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Location", cascade={"persist"}, inversedBy="jobs")
     */
    private $location;

    function __construct()
    {
        $this->location = new Location();
        $this->firm = new Company();
    }

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
     * @param  string $title
     * @return Job
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
     * @param  string $company
     * @return Job
     */
    public function setCompany($company)
    {
        $this->firm->setTitle($company);

        return $this;
    }

    /**
     * Get company
     *
     * @return string
     */
    public function getCompany()
    {
        return $this->firm ? $this->firm->getTitle() : null;
    }

    /**
     * Set contractType
     *
     * @param  string $contractType
     * @return Job
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
     * @param  string $description
     * @return Job
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
     * @param  string $howToApply
     * @return Job
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
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set status
     *
     * @param  integer $status
     * @return Job
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

    public function isNew()
    {
        return $this->status === self::STATUS_NEW;
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

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    public function isPublished()
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function isDeleted()
    {
        return $this->status === self::STATUS_DELETED;
    }

    /**
     * Set visibleFrom
     *
     * @param  \DateTime $visibleFrom
     * @return Job
     */
    public function setVisibleFrom($visibleFrom)
    {
        $this->visibleFrom = $visibleFrom;

        return $this;
    }

    /**
     * Get visibleFrom
     *
     * @return \DateTime
     */
    public function getVisibleFrom()
    {
        return $this->visibleFrom;
    }

    /**
     * Set visibleTo
     *
     * @param  \DateTime $visibleTo
     * @return Job
     */
    public function setVisibleTo($visibleTo)
    {
        $this->visibleTo = $visibleTo;

        return $this;
    }

    /**
     * @return Location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param mixed $location
     */
    public function setLocation(Location $location)
    {
        $this->location = $location;
    }

    /**
     * Get visibleTo
     *
     * @return \DateTime
     */
    public function getVisibleTo()
    {
        return $this->visibleTo;
    }

    /**
     * Used as a getter validator
     *
     * @return bool
     *
     * @Assert\True(message="End date should be greater than start date", groups={"admin"})
     */
    public function isVisibleDatesValid()
    {
        return ($this->visibleFrom < $this->visibleTo);
    }

    /**
     * @return datetime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param  \DateTime $deletedAt
     * @return $this
     */
    public function setDeletedAt(\DateTime $deletedAt)
    {
        $this->deletedAt = $deletedAt;
    }

    /**
     * @return datetime
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * @return string|bool
     */
    public function getTimeLeft()
    {
        $currentDate = new \DateTime();

        if (null !== $this->visibleTo) {
            $interval = $currentDate->diff($this->visibleTo);

            if ($interval->format('%a') == 0) {
                return $interval->format('%h hour(s) left');
            }

            return $interval->format('%a day(s) left');
        }

        return false;
    }

    public function getFeedItemTitle()
    {
        return $this->title;
    }

    public function getFeedItemDescription()
    {
        return $this->description;
    }

    public function getFeedItemPubDate()
    {
        return $this->visibleFrom;
    }

    public function getFeedItemLink()
    {
        return 'http://symfony.com';
    }

    /**
     * Set country
     *
     * @param string $country
     * @return Job
     */
    public function setCountry($country)
    {
        $this->location->setCountry($country);

        return $this;
    }

    /**
     * Get Country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->location ? $this->location->getCountry() : null;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return Job
     */
    public function setCity($city)
    {
        $this->location->setCity($city);

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->location ? $this->location->getCity() : null;
    }

    /**
     * @return mixed
     */
    public function getFirm()
    {
        return $this->firm;
    }

    /**
     * @param mixed $firm
     */
    public function setFirm($firm)
    {
        $this->firm = $firm;

        return $this;
    }
}
