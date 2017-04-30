<?php

namespace AmbigussBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MAGType extends AbstractType
{

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('motAmbigu', TextType::class, array(
				'required' => false,
			))
			->add('rechercherMA', SubmitType::class, array(
				'label' => 'Rechercher',
				'attr' => array('class' => 'btn btn-primary'),
			))
			->add('glose', TextType::class, array(
				'required' => false,
			))
			->add('rechercherG', SubmitType::class, array(
				'label' => 'Rechercher',
				'attr' => array('class' => 'btn btn-primary'),
			));
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'ambigussbundle_MAG';
	}

}
