<?php

namespace SensioLabs\BackendBundle\Admin;

use Symfony\Component\Security\Core\SecurityContext;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use SensioLabs\JobBoardBundle\Entity\Job;
use SensioLabs\JobBoardBundle\Service\Mailer;

class JobAdmin extends Admin
{
    protected $baseRoutePattern = '/';

    private $securityContext;

    private $mailer;

    private $adminEmail;

    public function __construct($code, $class, $baseControllerName, SecurityContext $securityContext, Mailer $mailer, $adminEmail)
    {
        parent::__construct($code, $class, $baseControllerName);

        $this->securityContext = $securityContext;
        $this->mailer = $mailer;
        $this->adminEmail = $adminEmail;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('create');
        $collection->remove('delete');
        $collection->add('safe-delete', $this->getRouterIdParameter().'/safe-delete');
        $collection->add('restore', $this->getRouterIdParameter().'/restore');
    }

    public function createQuery($context = 'list')
    {
        $status = $this->getRequest()->query->get('status', 'Published');

        $query = parent::createQuery($context);
        $query->andWhere(
            $query->expr()->eq($query->getRootAlias() . '.status', ':status')
        );
        $query->setParameter('status', $status);

        return $query;
    }

    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'createdAt'
    );

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('company')
            ->add('title')
            ->add('createdAt')
            ->add('listDisplays', null, array('label' => 'List Displays'))
            ->add('viewDisplays', null, array('label' => 'View Displays'))
            ->add('extDisplays', null, array('label' => 'Ext Displays'))
            ->add('_action', 'actions', array(
                'actions' => array(
                    'edit' => array(),
                    'delete_restore' => array(
                        'template' => 'SensioLabsBackendBundle:Includes:delete_restore.html.twig'
                    ),
                )
            ))
        ;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $contractTypes = array(
            Job::FULL_TIME,
            Job::PART_TIME,
            Job::INTERNSHIP,
            Job::FREELANCE,
            Job::ALTERNANCE
        );

        $formMapper
            ->add('title', null, array('attr' => array('class' => '')))
            ->add('company', null, array('attr' => array('class' => '')))
            ->add('country', 'country', array('attr' => array('class' => '')))
            ->add('city', null, array('attr' => array('class' => '')))
            ->add('contractType', 'choice', array('attr' => array('class' => ''), 'choices' => array_combine($contractTypes, $contractTypes)))
            ->add('visibleFrom', 'date', array('attr' => array('class' => ''), 'widget' => 'single_text'))
            ->add('visibleTo', 'date', array('attr' => array('class' => ''), 'widget' => 'single_text'))
            ->add('description','textarea', array('attr' => array('class' => ''), 'attr' => array('style' => 'width: 100%', 'rows'  => 20, 'class' => 'ckeditor')))
            ->add('howToApply', null, array('attr' => array('class' => '')))
        ;
    }

    public function preUpdate($object)
    {
        $em = $this->getModelManager()->getEntityManager($this->getClass());
        $originalObject = $em->getUnitOfWork()->getOriginalEntityData($object);

        if (null === $originalObject['visibleTo']) {
            $this->sendNotificationEmail($object);
        }
    }

    private function sendNotificationEmail($object)
    {
        $user = $this->securityContext->getToken()->getUser();
        $body = $this->mailer->createBody('SensioLabsBackendBundle:Mail:updateNotification.html.twig',  array(
            'name' => $user->getUsername(),
            'job'  => $object,
        ));

        $this->mailer->sendMail(
            sprintf('Your job %s has been validated !', $object->getTitle()),
            $this->adminEmail,
            $user->getEmail(),
            $body
        );
    }

    public function getExportFormats()
    {
        return array();
    }

    public function getBatchActions()
    {
        return;
    }
}
