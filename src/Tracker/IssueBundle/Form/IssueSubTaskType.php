<?php

namespace Tracker\IssueBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

use Tracker\IssueBundle\Entity\Issue;

class IssueSubTaskType extends AbstractType
{
    /**
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * @param SecurityContextInterface $securityContext
     */
    public function __construct(SecurityContextInterface $securityContext)
    {
        $this->securityContext = $securityContext;
    }

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

        /**
         * @var $issueStory Issue
         */
        $issueStory = $options['issueStory'];

        $attributePriority = array_merge($attribute, array(
            'class' => 'TrackerIssueBundle:Priority',
            'property' => 'value'
        ));

        $attributeReporter = array_merge($attribute, array(
            'class' => 'TrackerUserBundle:User',
            'property' => 'username',
            'data' => $issueStory->getProject()->getMembers()->first()
        ));
//
        $attributeAssign = array_merge($attribute, array(
            'class' => 'TrackerUserBundle:User',
            'property' => 'username',
            'data' => $issueStory->getProject()->getMembers()->first()
        ));

        $builder
            ->add('summary', 'text', $attribute)
            ->add('priority', 'entity', $attributePriority)
            ->add('code', 'text', $attribute)
            ->add('description', null, $attribute)
            ->add('reporter', null, $attributeReporter)
            ->add('assignee', null, $attributeAssign)
            ->add('save', 'submit')
            ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Tracker\IssueBundle\Entity\Issue',
            'issueStory' => null
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'tracker_issueBundle_issueSubTask_form';
    }
}
