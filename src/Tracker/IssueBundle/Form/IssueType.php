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
        $attribute = array (
            'attr'=>array (
                'class'=>'form-control'
            ),
            'label_attr'=>array (
                'class'=>'col-sm-4 control-label'
            )
        );

        $builder
            ->add('project', null, $attribute)
            ->add('type', null, $attribute)

            ->add('summary', 'text', $attribute)
            ->add('priority', null, $attribute)

            ->add('code', 'text', $attribute)
            ->add('description', null, $attribute)

            ->add('status', null, $attribute)
//            ->add('resolution', null, $attribute)
            ->add('reporter', null, $attribute)
            ->add('assignee', null, $attribute)
//            ->add('collaborators', null, $attribute)
//            ->add('parent', null, $attribute)
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
