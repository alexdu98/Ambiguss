<?php

namespace AppBundle\Form\Phrase;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
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
	        ->add('contenu', HiddenType::class)
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
	        'data_class' => 'AppBundle\Entity\Phrase',
	        'contenu' => null,
	        'signale' => null,
	        'visible' => null,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'AppBundle_phrase';
    }

}
