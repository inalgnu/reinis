<?php

namespace SensioLabs\JobBoardBundle\Service;

use Symfony\Bundle\TwigBundle\TwigEngine;

class Mailer
{
    private $mailer;

    private $templating;

    public function __construct(\Swift_Mailer $mailer, TwigEngine $templating)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
    }

    public function sendMail($subject, $from, $to, $body)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($from)
            ->setTo($to)
            ->setBody($body)
        ;

        $this->mailer->send($message);
    }

    public function createBody($view, $parameters)
    {
        return $this->templating->render($view, $parameters);
    }
}
