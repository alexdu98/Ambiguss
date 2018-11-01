<?php

namespace AppBundle\Form\Game;

use AppBundle\Form\Phrase\PhraseGameType;
use AppBundle\Form\Reponse\ReponseGameType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GameType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reponses', CollectionType::class, array(
            	'entry_type' => ReponseGameType::class,
	            'allow_add' => false,
	            'allow_delete' => false,
	            'label' => false,
            ))
	        ->add('valider', SubmitType::class, array(
		        'label' => 'Valider',
		        'attr' => array('class' => 'btn btn-primary')
	        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Game'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'AppBundle_game';
    }

}
