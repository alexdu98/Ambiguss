<?php

namespace UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModifProfilType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Pseudo', TextType::class, array(
                'attr' => array('placeholder' => $options['pseudo'], 'pattern' => '^[a-zA-Z0-9-_]{3,32}$'),
                'invalid_message' => 'Pseudo invalide',
                'disabled' => true
            ))

            ->add('Mdp', RepeatedType::class, array(
                'type' => PasswordType::class,
                'options' => array('attr' => array('class' => 'password-field')),
                'first_options'  => array(
                    'label' => 'Nouveau mot de passe',
                    'attr' => array('placeholder' => 'Nouveau mot de passe', 'pattern' => '^.{6,72}$'),
                ),
                'second_options' => array(
                    'label' => 'Confirmation du mot de passe',
                    'attr' => array('placeholder' => 'Confirmation du mot de passe')
                ),
                'required' => false,
                'invalid_message' => 'Les mots de passe ne sont pas identiques.'
            ))

            ->add('Email', EmailType::class, array(
                'attr' => array('placeholder' => $options['email']),
                'required' => false
            ))


            ->add('Valider', SubmitType::class, array(
                'attr' => array(
                    'class' => 'btn btn-primary g-recaptcha',
                    'data-sitekey' => '6LcXBhkUAAAAAIMfOvKJODxXAhw-qG2VzGG2rppj',
                    'data-callback' => 'onSubmit'
                ),
            ))
            ->remove('Newsletter')
            ->remove('Conditions');
    }




    public function getParent(){
        return MembreType::class;
    }

}
