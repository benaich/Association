<?php

namespace Ben\AssociationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date_from', 'date', array('widget' => 'single_text'))
            ->add('date_to', 'date', array('widget' => 'single_text'))
            ->add('user')
            ->add('room')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ben\AssociationBundle\Entity\Reservation'
        ));
    }

    public function getName()
    {
        return 'ben_associationbundle_reservationtype';
    }
}
