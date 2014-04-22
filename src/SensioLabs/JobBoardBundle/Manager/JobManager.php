<?php

namespace SensioLabs\JobBoardBundle\Manager;

use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use SensioLabs\JobBoardBundle\Entity\Job;

class JobManager
{
    private $doctrine;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @param SessionInterface $session
     * @return bool|Job
     * @throws NotFoundHttpException
     */
    public function getJobFromSession(SessionInterface $session)
    {
        if ($id = $session->get('jobId')) {
            $em = $this->doctrine->getManager();
            $job = $em->getRepository('SensioLabsJobBoardBundle:Job')->findOneById($id);

            if (!$job) {
                if ($session->has('jobId')) {
                    $session->remove('jobId');
                }

                throw new NotFoundHttpException(sprintf('Unable to find job with id %s', $id));
            }

            return $job;
        }

        return false;
    }
}
