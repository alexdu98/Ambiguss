<?php

namespace App\Form\MotAmbigu;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MotAmbiguAddPhraseType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('valeur', TextType::class, array(
				'label' => 'Mot ambigu',
				'attr' => array('placeholder' => 'Mot ambigu', 'class' => 'amb'),
				'invalid_message' => 'Mot ambigu invalide',
			))
			->add('gloses', EntityType::class, array(
				'class' => 'App\Entity\Glose',
				'choice_label' => 'valeur',
				'label' =>  'Glose associée',
				'mapped' => false,
				'required' => true,
				'attr' => array(
					'class' => 'gloses',
					'required' => 'required',
				),
			));
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'App\Entity\MotAmbigu'
		));
	}
	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'App_motambigu';
	}
}
