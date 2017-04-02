<?php

namespace AmbigussBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

class ReponseGameType extends AbstractType
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
		        'label' => '__glose__',
		        'attr' => array(
			        'class' => 'gloses',
			        'required' => 'required',
		        ),
	        ))
	        ->remove('motAmbiguPhrase')
            ->add('idMotAmbiguPhrase', HiddenType::class, array(
            	'attr' => array('class' => 'idMotAmbiguPhrase'),
	            'mapped' => false
            ))
	        ->add('motAmbigu', HiddenType::class, array(
		        'attr' => array('disabled' => true),
		        'mapped' => false,
		        'data' => '__motAmbigu__',
	        ));
    }
    
    public function getParent(){
	    return ReponseType::class;
    }

}
