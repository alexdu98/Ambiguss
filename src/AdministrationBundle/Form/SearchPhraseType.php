<?php

namespace AdministrationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SearchPhraseType extends AbstractType
{

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('idPhrase', NumberType::class, array(
				'attr' => array('placeholder' => 'Id phrase'),
				'required' => false,
			))
			->add('contenuPhrase', TextType::class, array(
				'attr' => array('placeholder' => 'Contenu phrase'),
				'required' => false,
			))
			->add('idAuteur', NumberType::class, array(
				'attr' => array('placeholder' => 'Id auteur'),
				'required' => false,
			))
			->add('PseudoOrEmailAuteur', TextType::class, array(
				'required' => false,
				'mapped' => false,
				'label' => 'Pseudo ou email auteur',
				'attr' => array('placeholder' => 'Pseudo ou email auteur'),
			))
			->add('Chercher', SubmitType::class);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'administrationbundle_phrase';
	}

}
