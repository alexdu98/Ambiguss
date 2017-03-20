<?php

namespace AmbigussBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PhraseAddType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('contenu', TextareaType::class, array(
				'label' => 'Phrase',
				'attr' => array('placeholder' => 'Phrase'),
				'invalid_message' => 'Phrase invalide'
			))
			->add('motsAmbigus', CollectionType::class, array(
				'entry_type' => MotAmbiguType::class,
				'allow_add' => true,
				'allow_delete' => true,
				'label' =>  false,
			))
			->add('creer', SubmitType::class, array(
				'label' => 'Créer',
				'attr' => array('class' => 'btn btn-primary'),
			));
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'AmbigussBundle\Entity\Phrase'
		));
	}

}
