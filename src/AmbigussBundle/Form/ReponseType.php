<?php

namespace AmbigussBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use UserBundle\Form\MembreType;
use UserBundle\Form\NiveauType;

class ReponseType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
	    $builder
		    ->add('valeurMotAmbigu', TextType::class, array(
			    'label' => 'Mot ambigu',
			    'disabled' => true,
			    'attr' => array('class' => 'amb'),
		    ))
		    ->add('valeurGlose', EntityType::class, array(
			    'class'        => 'AmbigussBundle:Glose',
			    'choice_label' => 'valeur',
			    'label' => 'Glose'
		    ));
    }

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'AmbigussBundle\Entity\Reponse'
		));
	}

}
