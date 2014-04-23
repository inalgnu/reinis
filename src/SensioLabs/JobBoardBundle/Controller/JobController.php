<?php

namespace SensioLabs\JobBoardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use SensioLabs\JobBoardBundle\Entity\Job;

class JobController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $country = $request->query->get('country');
        $contractType = $request->query->get('contract-type');
        $page = $request->query->get('page', 1);
        $maxPerPage = $this->container->getParameter('sensio_labs_job_board.max_per_page');

        $jobRepository =  $this->getDoctrine()->getRepository('SensioLabsJobBoardBundle:Job');
        $jobs = $jobRepository->getJobs($page, $country, $contractType, $maxPerPage);

        if ($request->isXmlHttpRequest()) {
            return $this->render('SensioLabsJobBoardBundle:Includes:job_container.html.twig', array('jobs' => $jobs));
        }

        return array(
            'jobs' => $jobs,
            'countries' => $jobRepository->getCountriesWithJob(),
            'contract_types' => $jobRepository->getContractTypesWithJob(),
        );
    }

    /**
     * @Route("/{country_code}/{contract_type}/{slug}/preview", name="job_preview")
     * @Template()
     */
    public function previewAction($slug)
    {
        $em = $this->getDoctrine()->getManager();
        $job = $em->getRepository('SensioLabsJobBoardBundle:Job')->findOneBySlug($slug);

        if (!$job) {
           throw $this->createNotFoundException(sprintf('Unable to find job with slug %s', $slug));
        }

        return array('job' => $job);
    }

    /**
     * @Route("/post", name="job_post")
     * @Template()
     */
    public function postAction(Request $request)
    {
        $jobManager = $this->container->get('sensiolabs.manager.job');

        if (!$job = $jobManager->getJobFromSession($request->getSession())) {
            $job = new Job();
        }

        $form = $this->createForm('job', $job);
        $form->handleRequest($request);

        if ($form->isValid()) {
            if (null === $job->getStatus()) {
                $jobManager->createJob($job);
                $jobManager->setJobIdInSession($request->getSession(), $job->getId());
            } else {
                $jobManager->updateJob($job);
            }

            return $this->redirect($this->generateUrl('job_preview', array(
                'country_code' => $job->getCountry(),
                'contract_type' => $job->getContractType(),
                'slug' => $job->getSlug(),
            )));
        }

        return array('form' => $form->createView());
    }

    /**
     * @Route("/{country_code}/{contract_type}/{slug}/update", name="job_update")
     * @Security("has_role('ROLE_USER')")
     * @Template("SensioLabsJobBoardBundle:Job:post.html.twig")
     */
    public function updateAction(Request $request, $slug)
    {
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();
        $job = $em->getRepository('SensioLabsJobBoardBundle:Job')->findOneBy(array('slug' => $slug));

        if (!$job || $user !== $job->getUser()) {
            throw $this->createNotFoundException(sprintf('Unable to find job with slug %s', $slug));
        }

        $this->container->get('sensiolabs.manager.job')->setJobInSession($request, $job);
        $form = $this->createForm('job', $job);

        return array('form' =>  $form->createView());
    }

    /**
     * @Route("/{country_code}/{contract_type}/{slug}", name="job_show", requirements={"country_code"= "[A-Z]{2}" } )
     * @Template()
     */
    public function showAction($slug)
    {
        $em = $this->getDoctrine()->getManager();
        $job = $em->getRepository('SensioLabsJobBoardBundle:Job')->findOneBySlug($slug);

        if (!$job) {
            throw $this->createNotFoundException(sprintf('Unable to find job with slug %s', $slug));
        }

        return array('job' => $job);
    }

    /**
     * @Route("/manage", name="manage")
     * @Template()
     */
    public function manageAction()
    {
        return array();
    }
}
