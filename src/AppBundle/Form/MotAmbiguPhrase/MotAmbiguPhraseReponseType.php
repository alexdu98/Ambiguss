<?php

namespace AppBundle\Form\MotAmbiguPhrase;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class MotAmbiguPhraseReponseType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
	        ->add('ordre')
	        ->add('phrase')
	        ->add('motAmbigu');
    }

	public function getParent(){
		return MotAmbiguPhraseType::class;
	}

}
