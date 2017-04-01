<?php
namespace JudgmentBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
class JugementAddType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('description', TextType::class, array(
                'label' => false,
                'attr' => array('placeholder' => 'Détaillez le motif de signalement..'))
        )

            ->add('categorieJugement',EntityType::class, array(
            'class' => 'JudgmentBundle\Entity\CategorieJugement',
            'choice_label' => 'CategorieJugement',
            'label' =>  'Categorie',
            'mapped' => false,
            'required' => true)
            )
            /* ->add('typeObjet',EntityType::class, array( // Cas: signaler les objets (gloses,mot ambigu et phrase) dans une seule modal
           'class' => 'JudgmentBundle\Entity\TypeObjet',
           'choice_label' => 'typeobjet',
           'label' =>  'Objet',
           'mapped' => false,
           'required' => true)
       )*/
            ->remove('typeObjet')
            ->remove('dateCreation')
            ->remove('dateDeliberation')
            ->remove('idObjet')
            ->remove('verdict')
            ->remove('auteur')
            ->remove('juge')
            ->add('ajouter', SubmitType::class, array(
                'label' => 'Ajouter',
                'attr' => array('class' => 'btn btn-primary')
            ));


    }

    public function getParent(){
        return JugementType::class;
    }



}