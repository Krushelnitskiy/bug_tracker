<?php

namespace Tracker\IssueBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

use Tracker\UserBundle\Entity\User;
use Tracker\IssueBundle\Entity\Type;
use Tracker\ProjectBundle\Entity\Project;

class IssueType extends AbstractType
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
        $projects = $options['projects'];

        /**
         * @var $user User
         */
        $user = $this->securityContext->getToken()->getUser();

        if ($builder->getData()->getId() == null) {
            if (count($projects) === 0) {
                $project = $user->getProject()->first();
            } else {
                $project = $projects[0];
            }
        } else {
            $project = $builder->getData()->getProject();
        }

        $attribute = $this->getDefaultAttribute();
        $attributeProject = $this->getAttributeProject($project, $user);
        $attributePriority = $this->getAttributePriority();
        $attributeStatus = $this->getAttributeStatus();
        $attributeReporter = $this->getAttributeReporter($builder, $user);
        $attributeAssign = $this->getAttributeAssign($builder, $user);
        $attributeType = $this->getAttributeType();
        $attributeSubmit = $this->getAttributeSubmit($builder);

        if ($builder->getData()->getid() != null) {
            $builder->add('status', 'entity', $attributeStatus);
            if (Type::TYPE_STORY !== $builder->getData()->getType()->getValue()) {
                $builder->add('type', 'entity', $attributeType);
            }
        } else {
            $builder->add('type', 'entity', $attributeType);
        }

        $builder
            ->add('project', 'entity', $attributeProject)
            ->add('summary', 'text', $attribute)
            ->add('priority', 'entity', $attributePriority)
            ->add('code', 'text', $attribute)
            ->add('description', null, $attribute)
            ->add('reporter', null, $attributeReporter)
            ->add('assignee', null, $attributeAssign)
            ->add('save', 'submit', $attributeSubmit);
    }

    /**
     * @param FormBuilderInterface $builder
     * @return array
     */
    protected function getAttributeSubmit(FormBuilderInterface $builder)
    {
        if (!$builder->getData()->getid()) {
            $attributes = array('label' => 'issue.form.create');
        } else {
            $attributes = array('label' => 'issue.form.update');
        }

        return $attributes;
    }

    /**
     * @param Project $project
     * @param User $user
     * @return array
     */
    protected function getAttributeProject(Project $project, User $user)
    {
        $attribute = $this->getDefaultAttribute();

        return array_merge($attribute, array(
            'class' => 'TrackerProjectBundle:Project',
            'property' => 'label',
            'data' => $project,
            'query_builder' => function ($er) use ($user) {
                $query = $er->createQueryBuilder('p');
                if (!$user->hasRole(User::ROLE_ADMIN) && !$user->hasRole(User::ROLE_MANAGER)) {
                    $query = $query->join('p.members', 'pu')
                        ->where('pu.id = :user')
                        ->setParameter('user', $user->getId());
                }
                return $query;
            }
        ));
    }

    /**
     * @param FormBuilderInterface $builder
     * @param User $user
     *
     * @return array
     */
    protected function getAttributeReporter(FormBuilderInterface $builder, User $user)
    {
        $attribute = $this->getDefaultAttribute();
        $reporter = $builder->getData()->getReporter();
        return array_merge($attribute, array(
            'class' => 'TrackerUserBundle:User',
            'property' => 'username',
            'data' => $reporter ? $reporter : $user
        ));
    }


    /**
     * @param FormBuilderInterface $builder
     * @param User $user
     *
     * @return array
     */
    protected function getAttributeAssign(FormBuilderInterface $builder, User $user)
    {
        $attribute = $this->getDefaultAttribute();
        $assignee = $builder->getData()->getAssignee();
        return array_merge($attribute, array(
            'class' => 'TrackerUserBundle:User',
            'property' => 'username',
            'data' => $assignee ? $assignee : $user
        ));
    }


    /**
     * @return array
     */
    protected function getAttributePriority()
    {
        $attribute = $this->getDefaultAttribute();

        return array_merge($attribute, array(
            'class' => 'TrackerIssueBundle:Priority',
            'property' => 'value'
        ));
    }

    /**
     * @return array
     */
    protected function getAttributeStatus()
    {
        $attribute = $this->getDefaultAttribute();

        return array_merge($attribute, array(
            'class' => 'TrackerIssueBundle:Status',
            'property' => 'value'
        ));
    }

    protected function getDefaultAttribute()
    {
        return array(
            'attr' => array(
                'class' => 'form-control'
            ),
            'label_attr' => array(
                'class' => 'col-sm-4 control-label'
            )
        );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Tracker\IssueBundle\Entity\Issue',
            'projects' => array()
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'tracker_issueBundle_issue';
    }

    /**
     * @return array
     */
    protected function getAttributeType()
    {
        $attribute = $this->getDefaultAttribute();
        $attributeType = array_merge($attribute, array(
            'class' => 'TrackerIssueBundle:Type',
            'property' => 'value',
            'query_builder' => function ($er) {
                return $er->createQueryBuilder('t')
                    ->where('t.value != :value')
                    ->setParameter('value', Type::TYPE_SUB_TASK);
            },
            'multiple' => false,
            'expanded' => false
        ));
        return $attributeType;
    }
}
