<?php

namespace AmbigussBundle\Form;

use AmbigussBundle\Form\GloseType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;


class MotEtGloseForm extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('Valeur_mot_ambi', TextType::class)
        ->add('Gloses_du_mot', CollectionType::class, array(
        'entry_type'   => GloseType::class,
        'allow_add'    => true,
        'allow_delete' => true
        ))
        ->add('Ajouter_glose', ButtonType::class);
        //->add('Retirer_mot', ButtonType::class);
    }


}
