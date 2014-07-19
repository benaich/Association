<?php

namespace Ben\AssociationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AvancementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date_from', 'date', array('widget' => 'single_text'))
            ->add('date_to', 'date', array('widget' => 'single_text'))
            ->add('city')
            ->add('user')
            ->add('status')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ben\AssociationBundle\Entity\Avancement'
        ));
    }

    public function getName()
    {
        return 'avancement_form';
    }
}
