<?php

namespace UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MembreOubliPassResetType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
	        ->add('Mdp', RepeatedType::class, array(
		        'type' => PasswordType::class,
		        'options' => array('attr' => array('class' => 'password-field')),
		        'first_options'  => array(
			        'label' => 'Mot de passe',
			        'attr' => array('placeholder' => 'Mot de passe')
		        ),
		        'second_options' => array(
			        'label' => 'Confirmation du mot de passe',
			        'attr' => array('placeholder' => 'Confirmation du mot de passe')
		        ),
		        'invalid_message' => 'Les mots de passe ne sont pas identiques.'
	        ))
	        ->add('Valider', SubmitType::class, array(
		        'attr' => array('class' => 'btn btn-primary'),
	        ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'UserBundle\Entity\Membre'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'userbundle_membre';
    }


}
