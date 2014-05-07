<?php

namespace SensioLabs\JobBoardBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Job
 *
 * @ORM\Table(name="sl_location")
 * @ORM\Entity()
 */
class Location
{
    /**
     * @ORM\OneToMany(targetEntity="Job", mappedBy="location")
     */
    private $jobs;

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

    private $currentJob;

    function __construct($country = null, $city = null)
    {
        $this->country = $country;
        $this->city = $city;
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
     * Set country
     *
     * @param string $country
     * @return Job
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
     * @return Job
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

    public function setCurrentJob($job)
    {
        $this->currentJob = $job;
    }

    public function getCurrentJob()
    {
        return $this->currentJob;
    }

    /**
     * @return mixed
     */
    public function getJobs()
    {
        return $this->jobs;
    }

    /**
     * @param mixed $jobs
     */
    public function setJobs($jobs)
    {
        $this->jobs = $jobs;
    }
}
