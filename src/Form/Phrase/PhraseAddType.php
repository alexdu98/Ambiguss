<?php

namespace App\Form\Phrase;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Form\MotAmbigu\MotAmbiguAddPhraseType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PhraseAddType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->remove('dateCreation')
			->remove('dateModification')
			->remove('signale')
			->remove('visible')
			->remove('auteur')
			->remove('modificateur')
			->add('motsAmbigusPhrase', CollectionType::class, array(
				'entry_type' => MotAmbiguAddPhraseType::class,
				'allow_add' => true,
				'allow_delete' => true,
				'label' =>  false,
			))
			->add('creer', SubmitType::class, array(
				'label' => 'Créer la phrase',
				'attr' => array('class' => 'btn btn-primary btn-phrase-editor'),
			))
			->add('modifier', SubmitType::class, array(
				'label' => 'Modifier la phrase',
				'attr' => array('class' => 'btn btn-warning btn-phrase-editor'),
			));
	}

	public function getParent(){
		return PhraseType::class;
	}

    public function getBlockPrefix()
    {
        return 'phrase';
    }
}
