<?php

namespace SensioLabs\JobBoardBundle\Manager;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use SensioLabs\JobBoardBundle\Entity\Job;

interface JobManagerInterface
{
    /**
     * @return mixed
     */
    public function getJobFromSession();

    /**
     * @param $id
     * @return void
     */
    public function setJobIdInSession($id);

    /**
     * @return void
     */
    public function removeJobIdFromSession();

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
     * @return bool
     */
    public function safeDelete(Job $job);

    /**
     * @param  Job  $job
     * @return bool
     */
    public function restore(Job $job);

    /**
     * @param  Job  $job
     * @return bool
     */
    public function publish(Job $job);
}
