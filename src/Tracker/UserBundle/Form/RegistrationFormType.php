<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 26.02.15
 * Time: 16:39
 */


namespace Tracker\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $attributeDefault = array(
            'label_attr' => array('class' => 'col-sm-4 control-label'),
            'translation_domain' => 'FOSUserBundle',
            'attr'=>array('class'=> 'orm-control')
        );

        $attributeEmail = array_merge($attributeDefault, array(
            'label' => 'form.email'
        ));

        $attributeUserName = array_merge($attributeDefault, array(
            'label' => 'form.username'
        ));

        $attrPasswordFirst = array_merge($attributeDefault, array(
            'label' => 'form.password'
        ));

        $attrPasswordSecond = array_merge($attributeDefault, array(
            'label' => 'form.password_confirmation'
        ));

        $builder
            ->add('email', 'email', $attributeEmail)
            ->add('username', null, $attributeUserName)
            ->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'options' => array('translation_domain' => 'FOSUserBundle'),
                'first_options' => $attrPasswordFirst,
                'second_options' => $attrPasswordSecond,
                'invalid_message' => 'fos_user.password.mismatch'
            ))
        ;
    }

    public function getParent()
    {
        return 'fos_user_registration';
    }

    public function getName()
    {
        return 'acme_user_registration';
    }
}
