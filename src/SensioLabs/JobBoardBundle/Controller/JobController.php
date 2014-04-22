<?php

namespace SensioLabs\JobBoardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
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

        $jobs = $this->getDoctrine()->getRepository('SensioLabsJobBoardBundle:Job')->getJobs($page, $country, $contractType, $maxPerPage);

        if ($request->isXmlHttpRequest()) {
            return $this->render('SensioLabsJobBoardBundle:Includes:job_container.html.twig', array('jobs' => $jobs));
        }

        $countries = $this->getDoctrine()->getRepository('SensioLabsJobBoardBundle:Job')->getCountriesWithJob();
        $contractTypes = $this->getDoctrine()->getRepository('SensioLabsJobBoardBundle:Job')->getContractTypesWithJob();

        return array(
            'jobs' => $jobs,
            'countries' => $countries,
            'contract_types' => $contractTypes,
        );
    }

    /**
     * @Route("/{country_code}/{contract_type}/{slug}/preview", name="job_preview")
     * @Template()
     */
    public function previewAction($slug)
    {
        $em = $this->getDoctrine()->getManager();
        $job = $em->getRepository('SensioLabsJobBoardBundle:Job')->findOneBy(array('slug' => $slug));

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
            $em = $this->getDoctrine()->getManager();
            $job->backup();

            if ($this->get('security.context')->isGranted('ROLE_USER')) {
                $job->setUser($this->getUser());
            }

            $em->persist($job);
            $em->flush();

            $session = $request->getSession();
            $session->set('jobId', $job->getId());

            return $this->redirect($this->generateUrl('job_preview', array(
                'country_code' => $job->getCountry(),
                'contract_type' => $job->getContractType(),
                'slug' => $job->getSlug(),
            )));
        }

        return array('form' => $form->createView());
    }

    /**
     * @Route("/show", name="job_show")
     * @Template()
     */
    public function showAction()
    {
        return array();
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
