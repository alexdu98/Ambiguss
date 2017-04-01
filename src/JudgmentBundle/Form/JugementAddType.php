<?php

namespace JudgmentBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class JugementAddType extends AbstractType
{

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{

		$builder
			->add('description', TextareaType::class, array(
				'label' => 'Description',
				'attr' => array('placeholder' => 'Détaillez le motif du signalement'),
			))
			->add('categorieJugement', EntityType::class, array(
				'class' => 'JudgmentBundle\Entity\CategorieJugement',
				'choice_label' => 'CategorieJugement',
				'label' => 'Catégorie',
				'mapped' => false,
				'required' => true,
				'attr' => array('placeholder' => 'Détaillez le motif du signalement'),
			))
			// Cas: signaler les objets (gloses,mot ambigu et phrase) dans une seule modal
			/*->add('typeObjet',EntityType::class, array(
			   'class' => 'JudgmentBundle\Entity\TypeObjet',
			   'choice_label' => 'typeobjet',
			   'label' =>  'Objet',
			   'mapped' => false,
			   'required' => true
			))*/
			->remove('typeObjet')
			->remove('dateCreation')
			->remove('dateDeliberation')
			->remove('idObjet')
			->remove('verdict')
			->remove('auteur')
			->remove('juge')
			->add('signaler', SubmitType::class, array(
				'label' => 'Signaler',
				'attr' => array('class' => 'btn btn-primary'),
			));

	}

	public function getParent()
	{
		return JugementType::class;
	}

}