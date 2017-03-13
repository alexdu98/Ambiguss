<?php

namespace AmbigussBundle\Form;

use AmbigussBundle\Entity\Reponse;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PhraseType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
	        ->add('contenu', TextareaType::class, array(
	        	'label' => 'Phrase',
		        'attr' => array('placeholder' => 'Phrase'),
		        'invalid_message' => 'Phrase invalide'
	        ))
	        ->add('creer', SubmitType::class, array(
		        'label' => 'Créer',
		        'attr' => array('class' => 'btn btn-primary'),
	        ))
            ->add('reponse', CollectionType::class, array(
            	'entry_type' => ReponseType::class,
	            'allow_add' => true,
	            'allow_delete' => true,
            	'label' =>  false,
	            'mapped' => false
            ));

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AmbigussBundle\Entity\Phrase'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'ambigussbundle_phrase';
    }


}
