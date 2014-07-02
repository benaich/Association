<?php

namespace Ben\AssociationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RoomsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('number')
            ->add('floor')
            ->add('max')
            ->add('free')
            ->add('type', 'choice', array('choices' => array('homme' => 'Homme','femme' => 'Femme'),
                    'required' => false,))
            ->add('hotel')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ben\AssociationBundle\Entity\Rooms'
        ));
    }

    public function getName()
    {
        return 'ben_roomstype';
    }
}
