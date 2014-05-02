<?php

namespace SensioLabs\JobBoardBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use SensioLabs\Connect\Api\Entity\User as ConnectApiUser;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(name="sl_user")
 * @ORM\Entity(repositoryClass="SensioLabs\JobBoardBundle\Repository\UserRepository")
 */
class User implements UserInterface
{
    const ROLE_DEFAULT = 'ROLE_USER';
    const ROLE_ADMIN = 'ROLE_ADMIN';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $uuid;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity="Job", mappedBy="user")
     */
    private $jobs;

    /**
     * @ORM\Column(type="array")
     */
    private $roles;

    public function __toString()
    {
        return $this->username;
    }

    public function __construct($uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * @param mixed $job
     */
    public function addJob(Job $job)
    {
        $this->jobs[] = $job;
    }

    public function updateFromConnect(ConnectApiUser $apiUser)
    {
        $this->username = $apiUser->getUsername();
        $this->name = $apiUser->getName();
        $this->email = $apiUser->getEmail();
    }

    /**
     * @return mixed
     */
    public function getJobs()
    {
        return $this->jobs;
    }

    public function getUuid()
    {
        return $this->uuid;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    public function addRole($role)
    {
        $role = strtoupper($role);
        if ($role === self::ROLE_DEFAULT) {
            return $this;
        }

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function getRoles()
    {
        $roles = $this->roles;

        $roles[] = static::ROLE_DEFAULT;

        return array_unique($roles);
    }

    public function setRoles(array $roles)
    {
        $this->roles = array();

        foreach ($roles as $role) {
            $this->addRole($role);
        }

        return $this;
    }

    public function isAdmin()
    {
        if (null !== $this->roles) {
            return in_array(self::ROLE_ADMIN, $this->roles);
        }

        return false;
    }

    public function getPassword()
    {
    }

    public function getSalt()
    {
    }

    public function eraseCredentials()
    {
    }
}
