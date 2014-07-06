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
            ->add('number', 'text', array('label'  => 'N° de la chambre'))
            ->add('max', 'text', array('label'  => 'Capacité'))
            ->add('free', 'text', array('label'  => 'Place libre'))
            ->add('floor', 'text', array('label'  => 'N° d\'étage'))
            ->add('type', 'choice', array('choices' => array('homme' => 'Homme','femme' => 'Femme'),
                    'required' => false,))
            ->add('hotel', null, array('label'  => 'Logement'))
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
