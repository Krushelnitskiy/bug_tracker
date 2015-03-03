<?php

namespace Tracker\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;

class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $attributeDefault = array(
            'label_attr' => array('class' => 'col-sm-4 control-label'),
            'translation_domain' => 'FOSUserBundle',
            'attr'=>array('class'=> 'form-control')
        );

//        $attribute = array(
//            'translation_domain' => 'FOSUserBundle',
//            'attr'=>array(
//                'class'=>'form-control'
//            ),
//            'label_attr'=>array(
//                'class'=>'col-sm-4 control-label'
//            )
//        );

//        $attributeEmail = array_merge($attributeDefault, array(
//            'label' => 'form.email',
//        ));
//
//        $attributeUserName = array_merge($attributeDefault, array(
//            'label' => 'form.username',
//        ));
//
//        $attrPasswordFirst = array_merge($attributeDefault, array(
//            'label' => 'form.password',
//        ));
//
//        $attrPasswordSecond = array_merge($attributeDefault, array(
//            'label' => 'form.password_confirmation',
//        ));

        $attrRole = array_merge(
            $attributeDefault,
            array(
                'choices' => array(
                    'ROLE_ADMIN' => 'role.admin',
                    'ROLE_MANAGER' => 'role.manager',
                    'ROLE_USER' => 'role.user'
                ),
                'label' => 'Roles',
                'expanded' => true,
                'multiple' => true,
                'mapped' => true,
            )
        );

        $attrEnabled = array_merge(
            $attributeDefault,
            array(
                'choices' => array(
                    '1' => 'Enable',
                    '0' => 'Disable',
                ),
                'label' => 'Enabled',
//                'expanded' => true,
//                'multiple' => true,
//                'mapped' => true,
            )
//            array( 'choice_list' =>
//                new ChoiceList(array(1, 0.5), array('Full', 'Half')),
//                'expanded'=>true
//            )
        );

        $attrPasswordFirst = array_merge($attributeDefault, array(
            'label' => 'form.password',
        ));

        $attrPasswordSecond = array_merge($attributeDefault, array(
            'label' => 'form.password_confirmation',
        ));


        $builder
            ->add('email', 'email', $attributeDefault)
            ->add('username', null, $attributeDefault)
//            ->add('fullName')
            ->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'options' => array('translation_domain' => 'FOSUserBundle'),
                'first_options' => $attrPasswordFirst,
                'second_options' => $attrPasswordSecond,
                'invalid_message' => 'fos_user.password.mismatch',
            ))
            ->add('roles', 'choice', $attrRole)
            ->add('enabled', 'choice', $attrEnabled)
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Tracker\UserBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'tracker_userbundle_user';
    }
}
