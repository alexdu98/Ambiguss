<?php

namespace AmbigussBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
	        ->remove('poidsReponse')
	        ->remove('niveau')
	        ->add('glose', EntityType::class, array(
		        'class' => 'AmbigussBundle\Entity\Glose',
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
