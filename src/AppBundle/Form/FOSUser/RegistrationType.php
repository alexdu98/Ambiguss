<?php
namespace AppBundle\Form\FOSUser;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', null, array(
                'label' => 'Pseudo',
                'attr' => array(
                    'placeholder' => 'Pseudo'
                )
            ))
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'options' => array(
                    'translation_domain' => 'FOSUserBundle',
                    'attr' => array(
                        'autocomplete' => 'new-password',
                    ),
                ),
                'first_options' => array(
                    'label' => 'Mot de passe',
                    'attr' => array(
                        'placeholder' => 'Mot de passe'
                    )
                ),
                'second_options' => array(
                    'label' => 'Confirmation du mot de passe',
                    'attr' => array(
                        'placeholder' => 'Confirmation du mot de passe'
                    )
                ),
                'invalid_message' => 'Les mots de passe ne correspondent pas',
            ))
            ->add('email', EmailType::class, array(
                'label' => 'Email',
                'attr' => array(
                    'placeholder' => 'Email'
                ),
                'translation_domain' => 'FOSUserBundle'
            ))
            ->add('Newsletter', CheckboxType::class, array(
                'label' => "J'accepte de recevoir les newsletter du site",
                'required' => false,
            ))
            ->add('Conditions', CheckboxType::class, array(
                'label' => "J'accepte les conditions d'utilisation du site",
                'required' => true,
                'mapped' => false,
                'constraints' => array(
                    new IsTrue(['message' => 'Les conditions d\'utilisation doivent être acceptés.'])
                )
            ));
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';
    }

    public function getBlockPrefix()
    {
        return 'user_registration';
    }
}
