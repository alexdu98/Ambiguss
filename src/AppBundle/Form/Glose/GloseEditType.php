<?php

namespace AppBundle\Form\Glose;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Security\Core\Security;

class GloseEditType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('valeur', TextType::class, array(
				'label' => 'Valeur',
				'attr' => array(
					'placeholder' => 'Éditez la glose',
					'maxlength' => 32,
				),
			))
			->remove('dateCreation')
			->remove('dateModification')
            ->add('visible', ChoiceType::class, array(
                'label' => 'Visible',
                'choices' => array(
                    'Oui' => '1',
                    'Non' => '0',
                ),
                'expanded' => true,
                'attr' => array('class' => 'radio-inline')
            ))
            ->add('signale', ChoiceType::class, array(
                'label' => 'Signalé',
                'choices' => array(
                    'Oui' => '1',
                    'Non' => '0',
                ),
                'expanded' => true,
                'attr' => array('class' => 'radio-inline')
            ))
			->remove('auteur')
			->remove('modificateur')
			->remove('motsAmbigus')
			->add('modifier', SubmitType::class, array(
				'label' => 'Modifier',
				'attr' => array('class' => 'btn btn-warning'),
			));
	}

	public function getParent()
	{
		return GloseType::class;
	}

}
