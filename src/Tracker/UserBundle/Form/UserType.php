<?php

namespace Tracker\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\TranslatorInterface;

use Tracker\UserBundle\Entity\Timezone;
use Tracker\UserBundle\Form\DataTransformer\StringToArrayTransformer;

class UserType extends AbstractType
{
    /**
     * @var $translator TranslatorInterface
     */
    protected $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $attributeDefault = $this->getDefaultAttributes();

        $attributeSubmit = $attributeDefault;
        unset($attributeSubmit['attr']);

        $builder
            ->add($this->getTimeZone($builder))
            ->add('email', 'email', $attributeDefault)
            ->add('username', null, $attributeDefault)
            ->add('fullName', null, $attributeDefault)
            ->add($this->getPlainPassword($builder))
            ->add($this->getRoles($builder))
            ->add($this->getEnabled($builder))
            ->add('file', null, $attributeSubmit);

        if (!$builder->getData()->getId()) {
            $builder->add('save', 'submit', array('label' => 'issue.form.create'));
        } else {
            $builder->add('save', 'submit', array('label' => 'issue.form.update'));
        }
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return FormBuilderInterface
     */
    protected function getTimeZone(FormBuilderInterface $builder)
    {
        $attributeTimeZone = array_merge(
            $this->getDefaultAttributes(),
            array(
                'choices' => Timezone::$timezones,
                'multiple' => false,
                'required' => true
            )
        );

        return $builder->create('timezone', 'choice', $attributeTimeZone);
    }

    /**
     * @return array
     */
    protected function getDefaultAttributes()
    {
        return array(
            'label_attr' => array('class' => 'col-sm-4 control-label'),
            'translation_domain' => 'FOSUserBundle',
            'attr'=>array('class'=> 'form-control')
        );
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return FormBuilderInterface
     */
    protected function getEnabled(FormBuilderInterface $builder)
    {
        $attrEnabled = array_merge(
            $this->getDefaultAttributes(),
            array(
                'choices' => array(
                    '1' => 'Enable',
                    '0' => 'Disable'
                ),
                'label' => 'Enabled'
            )
        );

        return $builder->create('enabled', 'choice', $attrEnabled);
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return FormBuilderInterface
     */
    protected function getPlainPassword(FormBuilderInterface $builder)
    {
        $attrPasswordFirst = array_merge(
            $this->getDefaultAttributes(),
            array(
                'label' => 'form.password'
            )
        );

        $attrPasswordSecond = array_merge(
            $this->getDefaultAttributes(),
            array(
                'label' => 'form.password_confirmation'
            )
        );

        $attributes = array(
            'type' => 'password',
            'options' => array('translation_domain' => 'FOSUserBundle'),
            'first_options' => $attrPasswordFirst,
            'second_options' => $attrPasswordSecond,
            'invalid_message' => 'fos_user.password.mismatch'
        );

        if ($builder->getData()->getid()) {
            $attributes ['required'] = false;
        }

        return $builder->create('plainPassword', 'repeated', $attributes);
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return \Symfony\Component\Form\FormConfigBuilderInterface
     */
    protected function getRoles(FormBuilderInterface $builder)
    {
        $transformer = new StringToArrayTransformer();

        $attrRole = array_merge(
            $this->getDefaultAttributes(),
            array(
                'choices' => array(
                    'ROLE_ADMIN' => $this->translator->trans('role.admin'),
                    'ROLE_MANAGER' => $this->translator->trans('role.manager'),
                    'ROLE_OPERATOR' => $this->translator->trans('role.operator')
                ),
                'label' => 'Roles',
                'expanded' => false,
                'multiple' => false,
                'mapped' => true
            )
        );

        return $builder->create('roles', 'choice', $attrRole)->addModelTransformer($transformer);
    }

    /**
     * {@inheritdoc}
     *
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Tracker\UserBundle\Entity\User'
        ));
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getName()
    {
        return 'tracker_userBundle_user';
    }
}
