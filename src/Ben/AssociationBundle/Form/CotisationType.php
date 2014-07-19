<?php

namespace Ben\AssociationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CotisationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('price')
            ->add('type', 'choice', array('choices' => array('Espèces' => 'Espèces','Chèque' => 'Chèque','Virement' => 'Virement')))
            ->add('date_from', 'date', array('widget' => 'single_text'))
            ->add('date_to', 'date', array('widget' => 'single_text'))
            ->add('description')
            ->add('user')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ben\AssociationBundle\Entity\Cotisation'
        ));
    }

    public function getName()
    {
        return 'cotisation_form';
    }
}
