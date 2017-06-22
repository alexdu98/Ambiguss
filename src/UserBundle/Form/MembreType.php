<?php

namespace UserBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MembreType extends AbstractType
{

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('pseudo')
			->add('email', EmailType::class)
			->add('mdp', PasswordType::class, array(
				'required' => false,
				'attr' => array('placeholder' => 'Ne pas remplir si on ne veut pas modifier'),
			))
			->add('dateInscription')
			->add('dateConnexion', DateTimeType::class, array(
				'required' => false
			))
			->add('sexe', ChoiceType::class, array(
				'choices' => array(
					'Homme' => 'Homme',
					'Femme' => 'Femme',
				),
				'required' => false,
			))
			->add('dateNaissance', BirthdayType::class, array(
				'required' => false,
			))
			->add('pointsClassement')
			->add('credits')
			->add('cleOubliMdp', TextType::class, array(
				'required' => false,
			))
			->add('newsletter', CheckboxType::class, array(
				'required' => false,
			))
			->add('banni', CheckboxType::class, array(
				'required' => false,
			))
			->add('commentaireBan', TextType::class, array(
				'required' => false,
			))
			->add('dateDeban', DateTimeType::class, array(
				'required' => false,
			))
			->add('actif', CheckboxType::class, array(
				'required' => false,
			))
			->add('groupe', EntityType::class, array(
				'class' => 'UserBundle:Groupe',
				'choice_label' => 'nom',
			))
			->add('niveau', EntityType::class, array(
				'class' => 'UserBundle:Niveau',
				'choice_label' => 'titre',
			))
			->add('idFacebook', TextType::class, array(
				'required' => false,
			))
			->add('idTwitter', TextType::class, array(
				'required' => false,
			))
			->add('Modifier', SubmitType::class, array(
				'label' => 'Modifier',
				'attr' => array(
					'class' => 'btn btn-warning',
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
