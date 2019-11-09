<?php

namespace AppBundle\Form\FOSUser;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfilEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->remove('username')
            ->remove('current_password')
            ->add('email', EmailType::class, array(
                'attr' => array('placeholder' => 'Email')
            ))
            ->add('Newsletter', CheckboxType::class, array(
                'label' => "J'accepte de recevoir les newsletter du site",
                'required' => false,
            ))
            ->add('Sexe', ChoiceType::class, array(
                'choices' => array(
                    'Homme' => 'Homme',
                    'Femme' => 'Femme',
                ),
                'label' => "Genre",
                'required' => false,
            ))
            ->add('dateNaissance', BirthdayType::class, array(
                'label' => 'Date de naissance',
                'placeholder' => array(
                    'year' => 'Année',
                    'month' => 'Mois',
                    'day' => 'Jour',
                ),
                'required' => false
            ))
            ->add('Valider', SubmitType::class, array(
                'label' => 'Valider',
                'attr' => array(
                    'class' => 'btn btn-primary',
                ),
            ));

        if($builder->getData()->getRenamable()) {
            $builder
                ->add('username', null, array(
                    'required' => false,
                    'label' => 'Pseudo',
                    'attr' => array(
                        'placeholder' => 'Pseudo',
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'top',
                        'title' => 'Ne peut être modifié qu\'une seule fois'
                    )
                ));
        }
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\ProfileFormType';
    }

    public function getBlockPrefix()
    {
        return 'user_profile_edit';
    }
}
