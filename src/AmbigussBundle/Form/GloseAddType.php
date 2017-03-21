<?php

namespace AmbigussBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GloseAddType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
	        ->add('valeur', TextType::class, array(
	        	'label' => false,
	        	'attr' => array('placeholder' => 'Saisissez une nouvelle glose')
	        ))
	        ->remove('dateCreation')
	        ->remove('dateModification')
	        ->remove('signale')
	        ->remove('visible')
	        ->remove('auteur')
	        ->remove('modificateur')
	        ->remove('motsAmbigus')
            ->add('ajouter', SubmitType::class, array(
            	'label' => 'Ajouter',
	            'attr' => array('class' => 'btn btn-primary')
            ));
    }
    
   public function getParent(){
	   return GloseType::class;
   }

}
