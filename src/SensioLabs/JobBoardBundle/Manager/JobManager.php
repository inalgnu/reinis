<?php

namespace SensioLabs\JobBoardBundle\Manager;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\User\UserInterface;
use SensioLabs\JobBoardBundle\Entity\Job;
use SensioLabs\JobBoardBundle\Service\Mailer;
use Finite\Factory\FactoryInterface;

class JobManager implements JobManagerInterface
{
    private $entityManager;

    private $securityContext;

    private $mailer;

    private $session;

    private $finiteFactory;

    private $adminEmail;

    public function __construct(EntityManager $entityManager, SecurityContext $securityContext, Mailer $mailer, SessionInterface $session, FactoryInterface $finiteFactory, $adminEmail)
    {
        $this->entityManager = $entityManager;
        $this->securityContext = $securityContext;
        $this->mailer = $mailer;
        $this->session = $session;
        $this->finiteFactory = $finiteFactory;
        $this->adminEmail = $adminEmail;
    }

    /**
     * @return mixed
     */
    public function getJobFromSession()
    {
        $id = $this->session->get('jobId');

        $job = $this->entityManager->getRepository('SensioLabsJobBoardBundle:Job')->findOneById($id);

        if (!$job) {
            $this->removeJobIdFromSession();
        }

        return $job;
    }

    /**
     * @param $id
     * @return void
     */
    public function setJobIdInSession($id)
    {
        $this->session->set('jobId', $id);
    }

    /**
     * @return void
     */
    public function removeJobIdFromSession()
    {
        $this->session->remove('jobId');
    }

    /**
     * @param Job $job
     */
    public function updateJob(Job $job)
    {
        $user = $this->securityContext->getToken()->getUser();
        if ($this->securityContext->isGranted('ROLE_USER')) {
            if ($job->getUser() === $user && $job->isPublished()) {
                $this->sendUpdateNotificationMail($job);
            } else {
                $job->setUser($user);
            }
        }

        $this->entityManager->flush();
    }

    /**
     * @param Job $job
     */
    public function createJob(Job $job)
    {
        $user = $this->securityContext->getToken()->getUser();
        if ($user instanceof UserInterface) {
            $job->setUser($user);
        }

        $job->setStatus(Job::STATUS_NEW);

        $this->entityManager->persist($job);
        $this->entityManager->flush();
    }

    /**
     * @param $job
     */
    public function sendUpdateNotificationMail(Job $job)
    {
        $user = $this->securityContext->getToken()->getUser();

        $body = $this->mailer->createBody('SensioLabsJobBoardBundle:Mail:updateNotification.html.twig',  array(
            'name' => $user->getUsername(),
            'job'  => $job,
        ));

        $this->mailer->sendMail(
            sprintf('User %s update her job', $user->getUsername()),
            $user->getEmail(),
            $this->adminEmail,
            $body
        );
    }

    /**
     * @param Job $job
     * @return bool
     */
    public function safeDelete(Job $job)
    {
        $stateMachine = $this->finiteFactory->get($job);

        if (!$stateMachine->can('delete')) {
            return false;
        }

        $stateMachine->apply('delete');

        $job->setDeletedAt(new \DateTime());
        $this->entityManager->flush();

        return true;
    }

    /**
     * @param Job $job
     * @return bool
     */
    public function restore(Job $job)
    {
        $stateMachine = $this->finiteFactory->get($job);

        if (!$stateMachine->can('restore')) {
            return false;
        }

        $stateMachine->apply('restore');
        $this->entityManager->flush();

        return true;
    }

    /**
     * @param Job $job
     * @return bool
     */
    public function publish(Job $job)
    {
        $stateMachine = $this->finiteFactory->get($job);

        if (!$stateMachine->can('publish')) {
            return false;
        }

        $stateMachine->apply('publish');
        $this->entityManager->flush();

        return true;
    }
}
