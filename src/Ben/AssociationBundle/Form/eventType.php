<?php

namespace Ben\AssociationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class eventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('type')
            ->add('date_from', 'date', array('widget' => 'single_text'))
            ->add('date_to', 'date', array('widget' => 'single_text'))
            ->add('description')
            ->add('type')
            ->add('groups', null, array('expanded' => "true", "multiple" => "true"))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ben\AssociationBundle\Entity\event'
        ));
    }

    public function getName()
    {
        return 'ben_eventtype';
    }
}
