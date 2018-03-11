<?php

namespace AppBundle\Form\Reponse;

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
	        	'class' => 'AppBundle\Entity\Membre',
	            'choice_label' => 'username'
	        ))
	        ->add('poidsReponse', EntityType::class, array(
		        'class' => 'AppBundle\Entity\PoidsReponse',
		        'choice_label' => 'poidsReponse'
	        ))
	        ->add('niveau', EntityType::class, array(
		        'class' => 'AppBundle\Entity\Niveau',
		        'choice_label' => 'titre'
	        ))
	        ->add('glose', EntityType::class, array(
		        'class' => 'AppBundle\Entity\Glose',
		        'choice_label' => 'valeur'
	        ))
	        ->add('motAmbiguPhrase', EntityType::class, array(
		        'class' => 'AppBundle\Entity\MotAmbiguPhrase',
		        'choice_label' => 'id'
	        ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Reponse'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'AppBundle_reponse';
    }


}
