<?php

namespace AppBundle\Form\Signalement;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SignalementType extends AbstractType
{

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('description')
			->add('dateCreation')
			->add('dateDeliberation')
			->add('objetId')
			->add('categorieSignalement')
			->add('typeObjet')
			->add('verdict')
			->add('auteur')
			->add('juge');
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'AppBundle\Entity\Signalement',
		));
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'AppBundle_signalement';
	}

}
