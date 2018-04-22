<?php

namespace AppBundle\Form\FOSUser;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class GroupeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array(
                'label' => 'Nom',
                'translation_domain' => 'FOSUserBundle'
            ))
            ->add('roles', CollectionType::class, array(
                'entry_type' => TextType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'required' => false,
                'label' => false
            ));
    }
    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\GroupFormType';
    }
    public function getBlockPrefix()
    {
        return 'user_group';
    }
}
