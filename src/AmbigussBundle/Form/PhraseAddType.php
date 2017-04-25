<?php
namespace AmbigussBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

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
				'attr' => array('class' => 'btn btn-primary'),
			))
			->add('modifier', SubmitType::class, array(
				'label' => 'Modifier la phrase',
				'attr' => array('class' => 'btn btn-warning'),
			));
	}

	public function getParent(){
		return PhraseType::class;
	}
}