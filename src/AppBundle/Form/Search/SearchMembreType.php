<?php

namespace AppBundle\Form\Search;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchMembreType extends AbstractType
{

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('Id', NumberType::class, array(
				'attr' => array('placeholder' => 'Id'),
				'required' => true,
			))
			->add('Pseudo', TextType::class, array(
				'mapped' => false,
				'label' => 'Pseudo',
				'attr' => array('placeholder' => 'Pseudo'),
				'required' => false,
			))
			->add('Chercher', SubmitType::class);
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'AppBundle\Entity\Membre',
		));
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'administrationbundle_membre';
	}

}
