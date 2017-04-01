<?php
namespace AmbigussBundle\Form;
use AmbigussBundle\Repository\PoidsReponseRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MotAmbiguAddPhraseType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('valeur', TextType::class, array(
				'label' => 'Mot ambigu',
				'attr' => array('placeholder' => 'Mot ambigu', 'class' => 'amb'),
				'invalid_message' => 'Mot ambigu invalide',
			))
			->add('gloses', EntityType::class, array(
				'class' => 'AmbigussBundle\Entity\Glose',
				'choice_label' => 'valeur',
				'label' =>  'Glose associée',
				'mapped' => false,
				'required' => true,
				'attr' => array(
					'class' => 'gloses',
					'required' => 'required',
				),
			))
			->add('poidsReponse', EntityType::class, array(
				'class' => 'AmbigussBundle\Entity\PoidsReponse',
				'choice_label' => 'label',
				'label' =>  'Choississez le poids de cette glose',
				'mapped' => false,
				'required' => true,
				'attr' => array('required' => 'required'),
				'query_builder' => function(PoidsReponseRepository $rep){
					return $rep->createQueryBuilder('pr')->orderBy('pr.ordre');
			    }
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