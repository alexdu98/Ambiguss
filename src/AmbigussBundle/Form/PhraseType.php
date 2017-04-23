<?php

namespace AmbigussBundle\Form;

use Symfony\Component\Form\AbstractType;
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
			    'label' => false,
			    'attr' => array('placeholder' => 'Saisissez votre phrase'),
			    'invalid_message' => 'Phrase invalide'
		    ))
	        ->add('dateCreation')
	        ->add('dateModification')
	        ->add('signale')
	        ->add('visible')
	        ->add('auteur')
	        ->add('modificateur');
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AmbigussBundle\Entity\Phrase',
            'contenu' => null,
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
