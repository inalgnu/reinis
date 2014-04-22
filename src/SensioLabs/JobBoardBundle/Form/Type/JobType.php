<?php

namespace SensioLabs\JobBoardBundle\Form\Type;

use SensioLabs\JobBoardBundle\Entity\Job;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class JobType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $contractTypes = array(
            Job::FULL_TIME,
            Job::PART_TIME,
            Job::INTERNSHIP,
            Job::FREELANCE,
            Job::ALTERNANCE
        );

        $builder
            ->add('title', 'text', array('attr' => array('placeholder' => 'Job Title')))
            ->add('company', 'text', array('attr' => array('placeholder' => 'Company')))
            ->add('country', 'country')
            ->add('city', 'text', array(
                'attr' => array('class' => 'location', 'placeholder' => 'City'),
                'max_length' => 80
            ))
            ->add('contractType', 'choice', array(
                'empty_value' => 'Type of contract',
                'choices' => array_combine($contractTypes, $contractTypes)
            ))
            ->add('description', 'textarea', array('attr' => array('style' => 'width: 100%', 'rows'  => 20, 'class' => 'ckeditor')))
            ->add('howToApply', 'text', array('attr' => array('placeholder' => 'Send your resume at ...'), 'required' => false))
        ;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'job';
    }
}
