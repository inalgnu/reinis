<?php

namespace SensioLabs\JobBoardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use SensioLabs\JobBoardBundle\Entity\Announcement;
use SensioLabs\JobBoardBundle\Form\Type\AnnouncementType;

class JobController extends Controller
{
    /**
     * @Route("/show", name="job_show")
     * @Template()
     */
    public function showAction()
    {
        return array();
    }

    /**
     * @Route("/preview", name="job_preview")
     * @Template()
     */
    public function previewAction()
    {
        return array();
    }

    /**
     * @Route("/post", name="job_post_form")
     * @Method({"GET"})
     * @Template("SensioLabsJobBoardBundle:Job:post.html.twig")
     */
    public function getPostFormAction(Request $request)
    {
        $form = $this->createForm(new AnnouncementType(), new Announcement(), array(
            'action' => $this->generateUrl('job_post'),
            'method' => 'POST',
        ));

        return array('form' => $form->createView());
    }

    /**
     * @Route("/post", name="job_post")
     * @Method({"POST"})
     * @Template()
     */
    public function postAction(Request $request)
    {
        $announcement = new Announcement();
        $form = $this->createForm(new AnnouncementType(), $announcement);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($announcement);
            $em->flush();

            return $this->redirect($this->generateUrl('job_preview'));
        }

        return array(
            'form'  => $form->createView(),
        );
    }

    /**
     * @Route("/update", name="job_update")
     * @Template()
     */
    public function updateAction(Request $request)
    {
        return array();
    }
}
