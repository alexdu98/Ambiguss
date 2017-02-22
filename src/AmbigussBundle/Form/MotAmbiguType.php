<?php

namespace AmbigussBundle\Form;

use AmbigussBundle\Form\GloseType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
class MotAmbiguType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('gloses', CollectionType::class, array(
        'entry_type'   => GloseType::class,
        'allow_add'    => true,
        'allow_delete' => true
        ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AmbigussBundle\Entity\MotAmbigu'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'ambigussbundle_motambigu';
    }


}
