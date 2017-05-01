<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('pseudo', TextType::class, array(
				'attr' => array('placeholder' => 'Pseudo'),
			))
			->add('email', EmailType::class, array(
				'attr' => array('placeholder' => 'Email'),
			))
			->add('message', TextareaType::class, array(
				'attr' => array(
					'placeholder' => 'Message',
					'rows' => 7,
				),
			))
			->add('envoyer', SubmitType::class, array(
				'attr' => array('class' => 'btn btn-primary'),
			));
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'appbundle_contact';
	}

}
