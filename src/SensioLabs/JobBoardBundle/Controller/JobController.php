<?php

namespace SensioLabs\JobBoardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
     * @Route("/{country_code}/{contract_type_text_slug}/{title_slug}/preview", name="job_preview")
     * @Template()
     */
    public function previewAction($title_slug)
    {
        $em = $this->getDoctrine()->getManager();
        $announcement = $em->getRepository('SensioLabsJobBoardBundle:Announcement')->findOneBy(array('title_slug' => $title_slug));

        if (!$announcement) {
           throw new NotFoundHttpException(sprintf('Unable to find announcement with title_slug %s', $title_slug));
        }

        return array(
            'job' => $announcement,
        );
    }

    /**
     * @Route("/post", name="job_post_form")
     * @Method({"GET"})
     * @Template("SensioLabsJobBoardBundle:Job:post.html.twig")
     */
    public function getPostFormAction()
    {
        if ($announcementId = $this->get('session')->get('announcement_id')) {
            $announcement = $this->getAnnouncement($announcementId);
        } else {
            $announcement =  new Announcement();
        }

        $form = $this->createForm('announcement', $announcement, array(
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
        $form = $this->createForm('announcement', $announcement);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($announcement);
            $em->flush();

            $this->get('session')->set('announcement_id', $announcement->getId());

            return $this->redirect($this->generateUrl('job_preview', array(
                'country_code' => $announcement->getCountry(),
                'contract_type_text_slug' => $announcement->getContractTypeTextSlug(),
                'title_slug' => $announcement->getTitleSlug()
            )));
        }

        return array('form' => $form->createView());
    }

    /**
     * @param $id
     * @return Announcement
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    private function getAnnouncement($id)
    {
        $em = $this->getDoctrine()->getManager();
        $announcement = $em->getRepository('SensioLabsJobBoardBundle:Announcement')->findOneById($id);

        if (!$announcement) {
            throw new NotFoundHttpException(sprintf('Unable to find announcement with id %s', $id));
        }

        return $announcement;
    }
}
