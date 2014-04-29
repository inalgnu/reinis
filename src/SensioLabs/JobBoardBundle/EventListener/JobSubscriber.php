<?php

namespace SensioLabs\JobBoardBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use SensioLabs\JobBoardBundle\Entity\Job;
use SensioLabs\JobBoardBundle\Entity\Location;

class JobSubscriber implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return array(
            'prePersist',
        );
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        if ($entity instanceof Job) {
            $existingLocation = $em->getRepository('SensioLabsJobBoardBundle:Location')->findOneBy(array(
                'country' => $entity->getCountry(),
                'city' => $entity->getCity(),
            ));

            $existingCompany = $em->getRepository('SensioLabsJobBoardBundle:Company')->findOneByTitle($entity->getCompany());

            if (null !== $existingLocation) {
                $entity->setLocation($existingLocation);
            }

            if (null !== $existingCompany) {
                $entity->setFirm($existingCompany);
            }
        }
    }
}
