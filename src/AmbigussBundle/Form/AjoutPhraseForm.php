<?php

namespace AmbigussBundle\Form;

use AmbigussBundle\Form\MotEtGloseForm;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;

class AjoutPhraseForm extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('Phrase', TextType::class)
        ->add('Mot_ambi_glose', CollectionType::class, array(
        'entry_type'   => MotEtGloseForm::class,
        'allow_add'    => true,
        'allow_delete' => true
        ))
        ->add('Autre_mot', ButtonType::class)
        ->add('Valider', SubmitType::class);
    }


}
