<?php
namespace AmbigussBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class PhraseEditType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->remove('dateCreation')
            ->remove('dateModification')
	        ->add('signale', ChoiceType::class, array(
		        'label' => 'Signalé',
		        'data' => $options['signale'],
		        'choices' => array(
			        'Oui' => 1,
			        'Non' => 0,
		        ),
		        'expanded' => true,
	        ))
	        ->add('visible', ChoiceType::class, array(
		        'label' => 'Visible',
		        'data' => $options['visible'],
		        'choices' => array(
			        'Oui' => 1,
			        'Non' => 0,
		        ),
		        'expanded' => true,
	        ))
            ->remove('auteur')
            ->remove('modificateur')
	        ->add('motsAmbigusPhrase', CollectionType::class, array(
		        'entry_type' => MotAmbiguAddPhraseType::class,
		        'allow_add' => true,
		        'allow_delete' => true,
		        'label' => false,
	        ))
	        ->add('modifier', SubmitType::class, array(
                'label' => 'Modifier la phrase',
                'attr' => array('class' => 'btn btn-warning'),
            ));
    }

    public function getParent(){
        return PhraseType::class;
    }
}