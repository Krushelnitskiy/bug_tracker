<?php

namespace Tracker\IssueBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

class IssueCommentType extends AbstractType
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
            'attr'=> array (
                'class'=>'form-control'
            ),
            'label_attr'=>array (
                'class'=>'col-sm-4 control-label'
            )
        );

        $builder->add('body', 'textarea', $attribute);

        if (!$builder->getData()->getid()) {
            $builder->add('save', 'submit', array('label' => 'issue.form.create'));
        } else {
            $builder->add('save', 'submit', array('label' => 'issue.form.update'));
        }
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Tracker\IssueBundle\Entity\Comment'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'tracker_issueBundle_comment_form';
    }
}
