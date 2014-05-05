<?php

namespace SensioLabs\JobBoardBundle\Repository;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Doctrine\ORM\EntityRepository;
use SensioLabs\JobBoardBundle\Entity\User;

class UserRepository extends EntityRepository implements UserProviderInterface
{
    private $adminUuids;
    /**
     * {@inheritDoc}
     */
    public function loadUserByUsername($uuid)
    {
        $user = $this->findOneByUuid($uuid);

        if (!$user) {
            $user = new User($uuid);
        }

        $this->refreshRoles($user);

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('class %s is not supported', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUuid());
    }

    private function refreshRoles(UserInterface $user)
    {
        if (null !== $this->adminUuids) {
            if (!$user->isAdmin() && in_array($user->getUuid(), $this->adminUuids)) {
                $user->setRoles(array(User::ROLE_ADMIN));
            }
        } elseif ($user->isAdmin()) {
            $user->setRoles(array(User::ROLE_DEFAULT));
        }
    }

    public function supportsClass($class)
    {
        return 'SensioLabs\JobBoardBundle\Entity\User' === $class;
    }

    public function setAdminUuids($adminUuids)
    {
        $this->adminUuids = $adminUuids;
    }
}
