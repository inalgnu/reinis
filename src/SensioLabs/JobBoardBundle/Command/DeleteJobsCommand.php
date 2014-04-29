<?php

namespace SensioLabs\JobBoardBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteJobsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('jobboard:delete')
            ->setDescription('All announcements having the status "deleted" are definitively delete')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->getRepository('SensioLabsJobBoardBundle:Job')->hardDelete();
    }
}
