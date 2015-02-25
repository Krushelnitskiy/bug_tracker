<?php

namespace Tracker\IssueBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class IssueType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('summary', 'text', array('attr' => array('class' => 'form-control')) )
            ->add('code' , 'text', array('attr' => array('class' => 'form-control')))
            ->add('description', null, array('attr' => array('class' => 'form-control')))
            ->add('created',null, array('attr' => array('class' => 'form-control')))
            ->add('updated',null, array('attr' => array('class' => 'form-control')))
            ->add('type',null, array('attr' => array('class' => 'form-control')))
            ->add('priority',null, array('attr' => array('class' => 'form-control')))
            ->add('status' ,null, array('attr' => array('class' => 'form-control')))
            ->add('resolution',null, array('attr' => array('class' => 'form-control')))
            ->add('reporter',null, array('attr' => array('class' => 'form-control')))
            ->add('assignee',null, array('attr' => array('class' => 'form-control')))
            ->add('collaborators',null, array('attr' => array('class' => 'form-control')))
            ->add('parent',null, array('attr' => array('class' => 'form-control')))
            ->add('project',null, array('attr' => array('class' => 'form-control')))

            ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Tracker\IssueBundle\Entity\Issue'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'tracker_issuebundle_issue';
    }
}
