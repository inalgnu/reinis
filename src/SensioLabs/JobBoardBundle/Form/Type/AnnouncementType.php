<?php

namespace SensioLabs\JobBoardBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AnnouncementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                'attr' => array(
                    'placeholder' => 'Job Title'
                ),
                'required' => true,
            ))
            ->add('company', 'text', array(
                'attr' => array(
                    'placeholder' => 'Company'
                ),
                'required' => true
            ))
            ->add('country', 'country', array(
                'required'  => true,
            ))
            ->add('city', 'text', array(
                'attr' => array(
                    'class' => 'location',
                    'placeholder' => 'City'
                ),
                'required' => true,
                'max_length' => 80,
            ))
            ->add('contract_type','choice', array(
                'choices' => array(
                    'empty_value' => 'Type of contract',
                    1 => 'Full Time',
                    2 => 'Part Time',
                    3 => 'Internship',
                    4 => 'Freelance',
                    5 => 'Alternance'
                ),
                'required' => true,
            ))
            ->add('description', 'textarea', array(
                'attr' => array(
                    'style' => 'width: 100%',
                    'rows'  => 20,
                    'class' => 'ckeditor',
                ),
                'required' => true,
            ))
            ->add('how_to_apply', 'text', array(
                'attr' => array(
                    'placeholder' => 'Send your resume at ...'
                ),
                'required'  => false,
            ))
        ;
    }

    public function getName()
    {
        return 'announcement';
    }
}
