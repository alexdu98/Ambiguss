<?php

namespace AmbigussBundle\Form;

use AmbigussBundle\Form\MotAmbiguType;
use AmbigussBundle\Form\PhraseType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FormType;

class MotAmbiguPhraseType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('phrase', PhraseType::class, array('label' => "Entrez votre phrase"))
        ->add('valeurMotAmbigu', TextType::class, array('label' => "Quel est le mot ambigu de cette phrase ?"))
        ->add('motAmbigu', MotAmbiguType::class);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AmbigussBundle\Entity\MotAmbiguPhrase'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'ambigussbundle_motambiguphrase';
    }


}
