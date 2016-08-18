<?php

namespace UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('email', null, array(
            'required' => true,
            'label' => 'Email',
        ));
        $builder->add('username', 'hidden', array(
        ));
        $builder->add('hasResetPassword', 'hidden', array(
        ));
        $builder->add('company', 'text', array(
            'label' => 'Company Name',
            'required' => true,
            'mapped' => false,
        ));
        $builder->add('firstName', null, array(
            'label' => 'First Name',
            'required' => true,
        ));
        $builder->add('lastName', null, array(
            'label' => 'Last Name',
            'required' => true,
        ));
    }

    public function getParent()
    {
//        return 'FOS\UserBundle\Form\Type\RegistrationFormType';

        // Or for Symfony < 2.8
         return 'fos_user_registration';
    }

    public function getBlockPrefix()
    {
        return 'app_user_registration';
    }

    // For Symfony 2.x
    public function getName()
    {
        return $this->getBlockPrefix();
    }

}
