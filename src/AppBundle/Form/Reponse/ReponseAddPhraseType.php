<?php

namespace AppBundle\Form\Reponse;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

class ReponseAddPhraseType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
	        ->remove('ip')
	        ->remove('dateReponse')
	        ->remove('contenuPhrase')
	        ->remove('valeurMotAmbigu')
	        ->remove('valeurGlose')
	        ->remove('auteur')
	        ->add('glose', EntityType::class, array(
		        'class' => 'AppBundle\Entity\Glose',
		        'choice_label' => 'valeur',
		        'label' => 'Glose'
	        ))
	        ->remove('motAmbiguPhrase')
            ->add('motAmbiguPhrase', HiddenType::class, array(
            	'attr' => array('class' => 'motAmbiguPhrase'),
	            'mapped' => false
            ));
    }
    
    public function getParent(){
	    return ReponseType::class;
    }

}
