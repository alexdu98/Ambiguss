<?php
namespace AmbigussBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
		        'choices' => array(
			        'Oui' => 1,
			        'Non' => 0,
		        ),
		        'expanded' => true,
	        ))
            ->remove('visible')
            ->remove('auteur')
            ->remove('modificateur')
	        ->add('modifier', SubmitType::class, array(
                'label' => 'Modifier la phrase',
                'attr' => array('class' => 'btn btn-warning'),
            ));
    }

    public function getParent(){
        return PhraseType::class;
    }
}