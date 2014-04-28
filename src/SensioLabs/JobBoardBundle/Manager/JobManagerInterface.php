<?php

namespace SensioLabs\JobBoardBundle\Manager;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use SensioLabs\JobBoardBundle\Entity\Job;

interface JobManagerInterface
{
    /**
     * @param  SessionInterface $session
     * @return mixed
     */
    public function getJobFromSession(SessionInterface $session);

    /**
     * @param  SessionInterface $session
     * @param $id
     * @return void
     */
    public function setJobIdInSession(SessionInterface $session, $id);

    /**
     * @param  SessionInterface $session
     * @return Job              or bool
     */
    public function removeJobIdFromSession(SessionInterface $session);

    /**
     * @param  Job  $job
     * @return void
     */
    public function updateJob(Job $job);

    /**
     * @param  Job  $job
     * @return void
     */
    public function createJob(Job $job);

    /**
     * @param  Job  $job
     * @return void
     */
    public function sendUpdateNotificationMail(Job $job);

    /**
     * @param  Job  $job
     * @return void
     */
    public function safeDelete(Job $job);

    /**
     * @param  Job  $job
     * @return void
     */
    public function restore(Job $job);

    /**
     * @param  Job  $job
     * @return void
     */
    public function publish(Job $job);
}
