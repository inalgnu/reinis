<?php

namespace SensioLabs\JobBoardBundle\Controller;

use Eko\FeedBundle\Field\Item\ItemField;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Intl\Intl;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use SensioLabs\JobBoardBundle\Entity\Job;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Exception\NotValidCurrentPageException;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Response;

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
        $maxPerPage = $this->container->getParameter('sensio_labs_job_board.max_per_page.homepage');

        $jobRepository = $this->getDoctrine()->getRepository('SensioLabsJobBoardBundle:Job');
        $jobs = $jobRepository->getJobs($page, $country, $contractType, $maxPerPage);

        $viewCounter = $this->container->get('sensiolabs.service.view_counter');
        foreach ($jobs as $job) {
            $viewCounter->incrementViewForRoute('job', $job->getId(), $request->attributes->get('_route'));
        }

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

        $this->container->get('sensiolabs.manager.job')->setJobIdInSession($request->getSession(), $job);
        $form = $this->createForm('job', $job);

        return array('form' =>  $form->createView());
    }

    /**
     * @Route("/{country_code}/{contract_type}/{slug}", name="job_show", requirements={"country_code"= "[A-Z]{2}" } )
     * @Template()
     */
    public function showAction(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        $job = $em->getRepository('SensioLabsJobBoardBundle:Job')->findOneBySlug($slug);

        if (!$job) {
            throw $this->createNotFoundException(sprintf('Unable to find job with slug %s', $slug));
        }

        $viewCounter = $this->container->get('sensiolabs.service.view_counter');
        $viewCounter->incrementViewForRoute('job', $job->getId(), $request->attributes->get('_route'));

        return array('job' => $job);
    }

    /**
     * @Route("/manage/{page}", name="manage", defaults={"page" = 1})
     * @Method("GET")
     * @Template()
     */
    public function manageAction($page)
    {
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository('SensioLabsJobBoardBundle:Job')->getByUserQuery($user);

        $adapter = new DoctrineORMAdapter($query);
        $pager = new Pagerfanta($adapter);
        $pager->setMaxPerPage($this->container->getParameter('sensio_labs_job_board.max_per_page.manage'));

        try {
            $pager->setCurrentPage($page);
        } catch (NotValidCurrentPageException $e) {
            throw new NotFoundHttpException();
        }

        return array('pager' => $pager);
    }

    /**
     * @Route("/api/random", name="api_job_random")
     */
    public function apiRandomAction(Request $request)
    {
        if (false === $this->get('security.context')->isGranted(null)) {
            return $this->redirect($this->generateUrl('homepage'));
        }

        $em = $this->getDoctrine()->getManager();
        $randomJob = $em->getRepository('SensioLabsJobBoardBundle:Job')->getRandomJob();

        $viewCounter = $this->container->get('sensiolabs.service.view_counter');
        $viewCounter->incrementViewForRoute('job', $randomJob->getId(), $request->attributes->get('_route'));

        $countryCode = $randomJob->getCountry();
        $contractType = $randomJob->getContractType();

        $response = new JsonResponse(array(
            'title'         => $randomJob->getTitle(),
            'company'       => $randomJob->getCompany(),
            'city'          => $randomJob->getCity(),
            'country_name'  => Intl::getRegionBundle()->getCountryName($countryCode),
            'country_code'  => $countryCode,
            'contract'      => $contractType,
            'url'           => $this->generateUrl('job_show', array(
                'country_code'  => $countryCode,
                'contract_type' => $contractType,
                'slug'          => $randomJob->getSlug(),
            )),
        ));

        return $response;
    }

    /**
     * @Route("/publish/{id}", name="job_publish")
     * @Security("has_role('ROLE_USER')")
     * @Method("GET")
     */
    public function publishAction(Request $request, Job $job)
    {
        if (!$job->getUser() === $this->getUser()) {
            throw new AccessDeniedException();
        }

        $jobManager = $this->container->get('sensiolabs.manager.job');
        $jobManager->publish($job);

        return new RedirectResponse($request->headers->get('referer'));
    }

    /**
     * @Route("/delete/{id}", name="job_delete")
     * @Security("has_role('ROLE_USER')")
     * @Method("GET")
     */
    public function deleteAction(Request $request, Job $job)
    {
        if (!$job->getUser() === $this->getUser()) {
            throw new AccessDeniedException();
        }

        $jobManager = $this->container->get('sensiolabs.manager.job');
        $jobManager->safeDelete($job);

        return new RedirectResponse($request->headers->get('referer'));
    }

    /**
     * Generate the article feed
     *
     * @Route("/rss", name="job_feed")
     * @return Response XML Feed
     */
    public function feedAction(Request $request)
    {
        $country = $request->query->get('country');
        $contractType = $request->query->get('contract-type');

        $jobs = $this->getDoctrine()->getRepository('SensioLabsJobBoardBundle:Job')->getFeed($country, $contractType);

        $feed = $this->get('eko_feed.feed.manager')->get('job');
        $feed->addFromArray($jobs);
        $feed->addItemField(new ItemField('company', 'getCompany'));
        $feed->addItemField(new ItemField('country', 'getCountry'));
        $feed->addItemField(new ItemField('city', 'getCity'));
        $feed->addItemField(new ItemField('contractType', 'getContractType'));
        $feed->addItemField(new ItemField('howToApply', 'getHowToApply'));

        return new Response($feed->render('rss'));
    }
}
