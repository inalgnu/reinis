<?php

namespace SensioLabs\JobBoardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class BaseController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $countryCode = $request->get('country-code');
        $contractType = $request->get('contract-type');
        $page = $request->get('page', 1);

        $jobs = $this->getDoctrine()->getRepository('SensioLabsJobBoardBundle:Announcement')->getAnnouncements($page, $countryCode, $contractType);

        if ($request->isXmlHttpRequest()) {
            return $this->render('SensioLabsJobBoardBundle:Includes:job_container.html.twig', array('jobs' => $jobs));
        }

        $countries = $this->getDoctrine()->getRepository('SensioLabsJobBoardBundle:Announcement')->getCountriesWithAnnouncement();
        $contractTypes = $this->getDoctrine()->getRepository('SensioLabsJobBoardBundle:Announcement')->getContractTypesWithAnnouncement();

        return array(
            'jobs' => $jobs,
            'countries' => $countries,
            'contract_types' => $contractTypes,
        );
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
