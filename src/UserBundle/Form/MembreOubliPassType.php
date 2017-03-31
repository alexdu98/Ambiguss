<?php

namespace UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MembreOubliPassType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
	        ->add('PseudoOuEmail', TextType::class, array(
		        'attr' => array('placeholder' => 'Pseudo ou email'),
		        'invalid_message' => 'Pseudo invalide',
	            'mapped' => false
	        ))
	        ->add('Valider', SubmitType::class, array(
		        'attr' => array(
		        	'class' => 'btn btn-primary g-recaptcha',
		            'data-sitekey' => '6LcXBhkUAAAAAIMfOvKJODxXAhw-qG2VzGG2rppj',
			        'data-callback' => 'onSubmit'
		        ),
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
