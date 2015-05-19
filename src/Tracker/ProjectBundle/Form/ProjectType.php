<?php

namespace Tracker\ProjectBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProjectType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $attribute = array(
            'attr'=>array(
                'class'=>'form-control'
            ),
            'label_attr'=>array(
                'class'=>'col-sm-4 control-label'
            )
        );

        $builder->add('label', null, $attribute)
            ->add('summary', null, $attribute)
            ->add('code', null, $attribute)
            ->add('code', null, $attribute)
            ->add('members', null, $attribute)
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Tracker\ProjectBundle\Entity\Project'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'tracker_projectBundle_project';
    }
}
