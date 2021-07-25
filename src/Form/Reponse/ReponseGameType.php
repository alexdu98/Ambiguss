<?php

namespace App\Form\Reponse;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReponseGameType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();

            $valeurMA = $data->getMotAmbiguPhrase()->getMotAmbigu()->getValeur();

            $form->add('glose', EntityType::class, array(
                'class' => 'App\Entity\Glose',
                'choice_label' => 'valeur',
                'label' => '__glose__',
                'attr' => array(
                    'class' => 'gloses',
                    'required' => 'required'
                ),
                'query_builder' => function (EntityRepository $er) use ($valeurMA) {
                    return $er->createQueryBuilder('g')
                        ->innerJoin("g.motsAmbigus", "ma", "WITH", "ma.valeur = :valeurMA")
                        ->setParameter('valeurMA', $valeurMA);
                }
            ));
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Reponse'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'App_reponse';
    }

}
