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
        if ($request->isXmlHttpRequest()) {
            $jobs = $this->getDoctrine()->getRepository('SensioLabsJobBoardBundle:Announcement')->getList($request->get('page'));

            return $this->render('SensioLabsJobBoardBundle:Includes:job_container.html.twig', array('jobs' => $jobs));
        }

        $jobs = $this->getDoctrine()->getRepository('SensioLabsJobBoardBundle:Announcement')->getList(1);

        return array('jobs' => $jobs);
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
