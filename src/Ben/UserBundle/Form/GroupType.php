<?php

namespace Ben\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class GroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('type', 'choice', array('choices' => array('groupe' => 'Groupe','commission' => 'commission')))
            ->add('image' , new \Ben\AssociationBundle\Form\imageType())
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ben\UserBundle\Entity\Group'
        ));
    }

    public function getName()
    {
        return 'group_form';
    }
}
