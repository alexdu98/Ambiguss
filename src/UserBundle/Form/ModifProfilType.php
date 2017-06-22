<?php

namespace UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class ModifProfilType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
	        ->remove('Pseudo')
	        ->remove('Valider')
	        ->add('mdpActu', PasswordType::class, array(
		        'label' => 'Mot de passe actuel',
		        'required' => false,
		        'mapped' => false,
		        'attr' => array('placeholder' => 'Mot de passe actuel'),
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
	        ->add('ValiderMdp', SubmitType::class, array(
		        'label' => 'Valider',
		        'attr' => array(
			        'class' => 'btn btn-primary',
		        ),
	        ))

            ->add('Email', EmailType::class, array(
                'attr' => array('placeholder' => $options['email']),
                'required' => false
            ))
	        ->add('ValiderEmail', SubmitType::class, array(
		        'label' => 'Valider',
                'attr' => array(
	                'class' => 'btn btn-primary',
                ),
            ))
	        ->add('Newsletter', CheckboxType::class, array(
		        'label' => "J'accepte de recevoir les newsletter du site",
		        'data' => $options['newsletter'],
		        'required' => false,
	        ))
	        ->add('Sexe', ChoiceType::class, array(
		        'choices' => array(
			        'Homme' => 'Homme',
			        'Femme' => 'Femme',
		        ),
		        'label' => "Genre",
		        'data' => $options['sexe'],
		        'required' => false,
	        ))
	        ->add('dateNaissance', BirthdayType::class, array(
		        'label' => 'Date de naissance',
		        'data' => $options['dateNaissance'],
		        'placeholder' => array(
			        'year' => 'Année',
			        'month' => 'Mois',
			        'day' => 'Jour',
		        ),
	        ))
	        ->add('ValiderInfos', SubmitType::class, array(
		        'label' => 'Valider',
		        'attr' => array(
			        'class' => 'btn btn-primary',
		        ),
	        ))

            ->remove('Conditions');
    }




    public function getParent(){
	    return MembreInscriptionType::class;
    }

}
