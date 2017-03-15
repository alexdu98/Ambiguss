<?php

namespace UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MembreConnexionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
	        ->add('Pseudo', TextType::class, array(
		        'attr' => array(
		        	'placeholder' => 'Pseudo'
		        ),
		        'invalid_message' => 'Pseudo invalide'
	        ))
	        ->add('Mdp', PasswordType::class, array(
		        'attr' => array(
		        	'placeholder' => 'Mot de passe'
		        ),
		        'label' => 'Mot de passe'
	        ))
	        ->add('KeepCo', CheckboxType::class, array(
		        'label' => 'Rester connecté',
		        'mapped' => false,
		        'required' => false
	        ))
	        ->add('Connexion', SubmitType::class, array(
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
