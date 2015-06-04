<?php

namespace Tracker\IssueBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use Tracker\UserBundle\Entity\User;
use Tracker\IssueBundle\Entity\Type;
use Tracker\ProjectBundle\Entity\Project;

class IssueType extends AbstractType
{
    /**
     * @var TokenStorageInterface
     */
    private $securityContext;

    /**
     * @param TokenStorageInterface $securityContext
     */
    public function __construct(TokenStorageInterface $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    /**
     * {@inheritDoc}
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $projects = $options['projects'];
        $project = array_key_exists('selectedProject', $options) ? $options['selectedProject'] : null ;

        /**
         * @var $user User
         */
        $user = $this->securityContext->getToken()->getUser();
        if (!$project) {
            if ($builder->getData()->getId() === null) {
                    $project = $projects[0];
            } else {
                $project = $builder->getData()->getProject();
            }
        }

        $attribute = $this->getDefaultAttribute();
        $attributeProject = $this->getAttributeProject($project, $projects);
        $attributePriority = $this->getAttributePriority();
        $attributeStatus = $this->getAttributeStatus();
        $attributeResolution = $this->getAttributeResolution();
        $attributeReporter = $this->getAttributeReporter($builder, $project, $user);
        $attributeAssign = $this->getAttributeAssign($builder, $project, $user);
        $attributeType = $this->getAttributeType();
        $attributeSubmit = $this->getAttributeSubmit($builder);

        if ($builder->getData()->getId() !== null) {
            $builder->add('status', 'entity', $attributeStatus);
            $builder->add('resolution', 'entity', $attributeResolution);
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
     *
     * @return array
     */
    protected function getAttributeSubmit(FormBuilderInterface $builder)
    {
        $attributes = array('label' => 'issue.form.update');
        if (!$builder->getData()->getid()) {
            $attributes = array('label' => 'issue.form.create');
        }

        return $attributes;
    }

    /**
     * @param Project $project
     * @param Project[] $projects
     *
     * @return array
     */
    protected function getAttributeProject(Project $project, $projects)
    {
        $attribute = $this->getDefaultAttribute();

        return array_merge($attribute, array(
            'class' => 'TrackerProjectBundle:Project',
            'property' => 'label',
            'data' => $project,
            'choices'=>$projects
        ));
    }

    /**
     * @param FormBuilderInterface $builder
     * @param Project $project
     * @param User $user
     *
     * @return array
     */
    protected function getAttributeReporter(FormBuilderInterface $builder, Project $project, User $user)
    {
        $attribute = $this->getDefaultAttribute();
        $reporter = $builder->getData()->getReporter();
        $selectedUser = $this->getSelectedUser($project, $user, $reporter);

        return array_merge($attribute, array(
            'class' => 'TrackerUserBundle:User',
            'property' => 'username',
            'data' => $selectedUser,
            'choices' => $project->getMembers()
        ));
    }

    /**
     * @param FormBuilderInterface $builder
     * @param Project $project
     * @param User $user
     *
     * @return array
     */
    protected function getAttributeAssign(FormBuilderInterface $builder, Project $project, User $user)
    {
        $attribute = $this->getDefaultAttribute();
        $assign = $builder->getData()->getAssignee();
        $selectedUser = $this->getSelectedUser($project, $user, $assign);

        return array_merge($attribute, array(
            'class' => 'TrackerUserBundle:User',
            'property' => 'username',
            'data' => $selectedUser,
            'choices' => $project->getMembers()
        ));
    }

    /**
     * @param Project $project
     * @param User $authUser
     * @param User|null $candidateUser
     *
     * @return User|User
     */
    protected function getSelectedUser(Project $project, User $authUser, $candidateUser)
    {
        $members = $project->getMembers();
        $selectedUser = $candidateUser ? $candidateUser : $authUser;
        if (!$members->contains($selectedUser)) {
            $selectedUser = $members->first();
        }

        return $selectedUser;
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


    /**
     * {@inheritDoc}
     *
     * @return array
     */
    protected function getAttributeResolution()
    {
        $attribute = $this->getDefaultAttribute();

        return array_merge($attribute, array(
            'class' => 'TrackerIssueBundle:Resolution',
            'property' => 'value'
        ));
    }

    /**
     * {@inheritDoc}
     *
     * @return array
     */
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
     * {@inheritdoc}
     *
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Tracker\IssueBundle\Entity\Issue',
            'projects' => array(),
            'selectedProject' => null
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
