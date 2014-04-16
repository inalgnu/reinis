<?php

namespace SensioLabs\JobBoardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use SensioLabs\JobBoardBundle\Entity\Announcement;

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
     * @Route("/{country_code}/{contract_type_slug}/{title_slug}/preview", name="job_preview")
     * @Template()
     */
    public function previewAction(Request $request)
    {
        $titleSlug = $request->attributes->get('title_slug');

        $em = $this->getDoctrine()->getManager();
        $announcement = $em->getRepository('SensioLabsJobBoardBundle:Announcement')->findOneBy(array('titleSlug' => $titleSlug));

        if (!$announcement) {
           throw $this->createNotFoundException(sprintf('Unable to find announcement with titleSlug %s', $titleSlug));
        }

        return array('job' => $announcement);
    }

    /**
     * @Route("/post", name="job_post")
     * @Method({"GET"})
     * @Template()
     */
    public function postAction()
    {
        $form = $this->createForm('announcement', $this->getAnnouncement(), array(
            'action' => $this->generateUrl('job_post_save'),
            'method' => 'POST'
        ));

        return array('form' => $form->createView());
    }

    /**
     * @Route("/post", name="job_post_save")
     * @Method({"POST"})
     * @Template("SensioLabsJobBoardBundle:Job:post.html.twig")
     */
    public function savePostAction(Request $request)
    {
        $announcement = $this->getAnnouncement();

        $form = $this->createForm('announcement', $announcement);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($announcement);
            $em->flush();

            $this->get('session')->set('announcement_id', $announcement->getId());

            return $this->redirect($this->generateUrl('job_preview', array(
                'country_code' => $announcement->getCountry(),
                'contract_type_slug' => $announcement->getContractTypeSlug(),
                'title_slug' => $announcement->getTitleSlug()
            )));
        }

        return array('form' => $form->createView());
    }

    /**
     * @return Announcement
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    private function getAnnouncement()
    {
        if ($id = $this->get('session')->get('announcement_id')) {
            $em = $this->getDoctrine()->getManager();
            $announcement = $em->getRepository('SensioLabsJobBoardBundle:Announcement')->findOneById($id);

            if (!$announcement) {
                throw $this->createNotFoundException(sprintf('Unable to find announcement with id %s', $id));
            }

            return $announcement;
        }

        return new Announcement();
    }
}
