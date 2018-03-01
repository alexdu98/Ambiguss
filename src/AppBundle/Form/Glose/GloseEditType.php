<?php

namespace AppBundle\Form\Glose;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class GloseEditType extends AbstractType
{

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('valeur', TextType::class, array(
				'label' => 'Valeur',
				'attr' => array(
					'placeholder' => 'Éditez la glose',
					'maxlength' => 32,
				),
			))
			->remove('dateCreation')
			->remove('dateModification')
			->add('signale', ChoiceType::class, array(
				'label' => 'Signalé',
				'choices' => array(
					'Oui' => 1,
					'Non' => 0,
				),
				'expanded' => true,
			))
			->remove('visible')
			->remove('auteur')
			->remove('modificateur')
			->remove('motsAmbigus')
			->add('modifier', SubmitType::class, array(
				'label' => 'Modifier',
				'attr' => array('class' => 'btn btn-warning'),
			));
	}

	public function getParent()
	{
		return GloseType::class;
	}

}
