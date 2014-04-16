<?php

namespace SensioLabs\JobBoardBundle\Form\Type;

use SensioLabs\JobBoardBundle\Entity\Announcement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AnnouncementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array('attr' => array('placeholder' => 'Job Title')))
            ->add('company', 'text', array('attr' => array('placeholder' => 'Company')))
            ->add('country', 'country')
            ->add('city', 'text', array(
                'attr' => array('class' => 'location', 'placeholder' => 'City'),
                'max_length' => 80
            ))
            ->add('contract_type','choice', array(
                'empty_value' => 'Type of contract',
                'choices' => array(
                    Announcement::FULL_TIME => Announcement::FULL_TIME,
                    Announcement::PART_TIME => Announcement::PART_TIME,
                    Announcement::INTERNSHIP => Announcement::INTERNSHIP,
                    Announcement::FREELANCE => Announcement::FREELANCE,
                    Announcement::ALTERNANCE => Announcement::ALTERNANCE
                )
            ))
            ->add('description', 'textarea', array('attr' => array('style' => 'width: 100%', 'rows'  => 20, 'class' => 'ckeditor')))
            ->add('how_to_apply', 'text', array('attr' => array('placeholder' => 'Send your resume at ...')))
        ;
    }

    public function getName()
    {
        return 'announcement';
    }
}
