<?php

namespace AppBundle\Form\Search;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SearchGloseType extends AbstractType
{

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('idGlose', NumberType::class, array(
				'attr' => array('placeholder' => 'Id glose'),
				'required' => false,
			))
			->add('contenuGlose', TextType::class, array(
				'attr' => array('placeholder' => 'Contenu glose'),
				'required' => false,
			))
			->add('idAuteur', NumberType::class, array(
				'attr' => array('placeholder' => 'Id auteur'),
				'required' => false,
			))
			->add('PseudoAuteur', TextType::class, array(
				'required' => false,
				'mapped' => false,
				'label' => 'Pseudo auteur',
				'attr' => array('placeholder' => 'Pseudo auteur'),
			))
			->add('Chercher', SubmitType::class);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'administrationbundle_glose';
	}

}
