<?php

namespace App\Form\Reponse;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReponseType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
	        ->add('ip')
	        ->add('dateReponse')
	        ->add('contenuPhrase')
	        ->add('valeurMotAmbigu')
	        ->add('valeurGlose')
	        ->add('auteur', EntityType::class, array(
	        	'class' => 'App\Entity\Membre',
	            'choice_label' => 'username'
	        ))
	        ->add('glose', EntityType::class, array(
		        'class' => 'App\Entity\Glose',
		        'choice_label' => 'valeur'
	        ))
	        ->add('motAmbiguPhrase', EntityType::class, array(
		        'class' => 'App\Entity\MotAmbiguPhrase',
		        'choice_label' => 'id'
	        ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Reponse'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'App_reponse';
    }


}
