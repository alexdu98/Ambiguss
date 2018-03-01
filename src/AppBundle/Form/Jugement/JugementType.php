<?php

namespace AppBundle\Form\Jugement;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JugementType extends AbstractType
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
			->add('idObjet')
			->add('categorieJugement')
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
			'data_class' => 'AppBundle\Entity\Jugement',
		));
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'judgmentbundle_jugement';
	}

}
