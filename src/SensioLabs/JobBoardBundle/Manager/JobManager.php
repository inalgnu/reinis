<?php

namespace SensioLabs\JobBoardBundle\Manager;

use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Model\UserInterface;
use \Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use SensioLabs\JobBoardBundle\Entity\Job;
use Symfony\Component\Security\Core\SecurityContext;

class JobManager
{
    private $entityManager;

    private $securityContext;

    private $mailer;

    private $templating;

    private $adminEmail;

    public function __construct(EntityManager $entityManager, SecurityContext $securityContext, \Swift_Mailer $mailer, TwigEngine $templating, $adminEmail)
    {
        $this->entityManager = $entityManager;
        $this->securityContext = $securityContext;
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->adminEmail = $adminEmail;
    }

    /**
     * @param SessionInterface $session
     * @return bool|Job
     * @throws NotFoundHttpException
     */
    public function getJobFromSession(SessionInterface $session)
    {
        if ($id = $session->get('jobId')) {
            $job = $this->entityManager->getRepository('SensioLabsJobBoardBundle:Job')->findOneById($id);

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

    /**
     * @param SessionInterface $session
     * @param $jobId
     */
    public function setJobIdInSession(SessionInterface $session, $jobId)
    {
        $session->set('jobId', $jobId);
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
    public function sendUpdateNotificationMail($job)
    {
        $user = $this->securityContext->getToken()->getUser();

        $message = \Swift_Message::newInstance()
            ->setSubject(sprintf('User %s update her job', $user->getUsername()))
            ->setFrom($user->getEmail())
            ->setTo($this->adminEmail)
            ->setBody($this->templating->render('SensioLabsJobBoardBundle:Mail:updateNotification.html.twig', array(
                'name' => $user->getUsername(),
                'job'  => $job,
            )))
        ;

        $this->mailer->send($message);
    }
}
