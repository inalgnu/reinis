<?php

namespace SensioLabs\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sonata\AdminBundle\Controller\CRUDController as Controller;

class JobAdminController extends Controller
{
    public function listAction()
    {
        if (false === $this->admin->isGranted('LIST')) {
            throw new AccessDeniedException();
        }

        $viewCounter = $this->get('sensiolabs.service.view_counter');

        $datagrid = $this->admin->getDatagrid();
        $formView = $datagrid->getForm()->createView();

        foreach($datagrid->getResults() as $object)
        {
            $object->listDisplays = $viewCounter->getViewForRoute('job', $object->getId(), 'homepage') ?: 0;
            $object->viewDisplays = $viewCounter->getViewForRoute('job', $object->getId(), 'job_show') ?: 0;
            $object->extDisplays = $viewCounter->getViewForRoute('job', $object->getId(), 'api_job_random') ?: 0;
        }

        $this->get('twig')->getExtension('form')->renderer->setTheme($formView, $this->admin->getFilterTheme());

        return $this->render($this->admin->getTemplate('list'), array(
            'action'     => 'list',
            'form'       => $formView,
            'datagrid'   => $datagrid,
            'csrf_token' => $this->getCsrfToken('sonata.batch'),
        ));
    }

    public function safeDeleteAction(Request $request)
    {
        $id     = $this->get('request')->get($this->admin->getIdParameter());
        $object = $this->admin->getObject($id);

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        if (false === $this->admin->isGranted('SAFE_DELETE', $object)) {
            throw new AccessDeniedException();
        }

        if (!$object->isPublished()) {
            $jobManager = $this->container->get('sensiolabs.manager.job');

            $jobManager->safeDelete($object);

            $this->addFlash('sonata_flash_success', sprintf('Job %s has been successfully deleted.', $object->getTitle()));
        } else {
            $this->addFlash('sonata_flash_error', sprintf('You cannot delete %s, it must not be published.', $object->getTitle()));
        }

        return new RedirectResponse($request->headers->get('referer'));
    }

    public function restoreAction(Request $request)
    {
        $id     = $this->get('request')->get($this->admin->getIdParameter());
        $object = $this->admin->getObject($id);

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        if (false === $this->admin->isGranted('RESTORE', $object)) {
            throw new AccessDeniedException();
        }

        if ($object->isDeleted()) {
            $jobManager = $this->container->get('sensiolabs.manager.job');

            $jobManager->restore($object);

            $this->addFlash('sonata_flash_success', sprintf('Job %s has been successfully restored.', $object->getTitle()));
        } else {
            $this->addFlash('sonata_flash_error', sprintf('You cannot restore %s, it must be deleted first.', $object->getTitle()));
        }

        return new RedirectResponse($request->headers->get('referer'));
    }
}
