<?php

namespace UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MembreInscriptionType extends AbstractType
{

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('Pseudo', TextType::class, array(
				'attr' => array(
					'placeholder' => 'Pseudo',
					'pattern' => '^[a-zA-Z0-9-_]{3,32}$',
				),
				'invalid_message' => 'Pseudo invalide',
			))
			->add('Mdp', RepeatedType::class, array(
				'type' => PasswordType::class,
				'options' => array('attr' => array('class' => 'password-field')),
				'first_options' => array(
					'label' => 'Mot de passe',
					'attr' => array(
						'placeholder' => 'Mot de passe',
						'pattern' => '^.{6,72}$',
					),
				),
				'second_options' => array(
					'label' => 'Confirmation du mot de passe',
					'attr' => array('placeholder' => 'Confirmation du mot de passe'),
				),
				'invalid_message' => 'Les mots de passe ne sont pas identiques.',
			))
			->add('Email', EmailType::class, array(
				'attr' => array('placeholder' => 'Email'),
			))
			->add('Newsletter', CheckboxType::class, array(
				'label' => "J'accepte de recevoir les newsletter du site",
				'required' => false,
			))
			->add('Conditions', CheckboxType::class, array(
				'label' => "J'accepte les conditions d'utilisation du site",
				'required' => true,
				'mapped' => false,
			))
			->add('Valider', SubmitType::class, array(
				'attr' => array(
					'class' => 'btn btn-primary g-recaptcha',
					'data-sitekey' => '6LcXBhkUAAAAAIMfOvKJODxXAhw-qG2VzGG2rppj',
					'data-callback' => 'onSubmit',
				),
			));
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'UserBundle\Entity\Membre',
			'pseudo' => null,
			'email' => null,
			'newsletter' => null,
			'sexe' => null,
			'dateNaissance' => null,
		));
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'userbundle_membre';
	}

}
