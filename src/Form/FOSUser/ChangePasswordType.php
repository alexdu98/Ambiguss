<?php

namespace App\Form\FOSUser;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $constraintsOptions = array(
            'message' => 'Mot de passe actuel invalide',
        );

        if (!empty($options['validation_groups'])) {
            $constraintsOptions['groups'] = array(reset($options['validation_groups']));
        }

        $builder
            ->add('current_password', PasswordType::class, array(
                'label' => 'Mot de passe actuel',
                'attr' => array('placeholder' => 'Mot de passe actuel'),
                'mapped' => false,
                'constraints' => array(
                    new NotBlank(),
                    new UserPassword($constraintsOptions),
                ),
            ))
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'first_options' => array(
                    'label' => 'Nouveau mot de passe',
                    'attr' => array('placeholder' => 'Nouveau mot de passe'),
                ),
                'second_options' => array(
                    'label' => 'Confirmation du mot de passe',
                    'attr' => array('placeholder' => 'Confirmation du mot de passe'),
                ),
                'invalid_message' => 'Le nouveau mot de passe et sa confirmation n\'est pas identique',
            ))
            ->add('Valider', SubmitType::class, array(
                'label' => 'Valider',
                'attr' => array(
                    'class' => 'btn btn-primary',
                ),
            ));
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\ChangePasswordFormType';
    }

    public function getBlockPrefix()
    {
        return 'user_change_password';
    }
}
