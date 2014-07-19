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
            ->add('date_from', 'datetime', array('widget' => 'single_text'))
            ->add('date_to', 'datetime', array('widget' => 'single_text'))
            ->add('description')
            ->add('type', 'choice', array('choices' => array('réunion d’information' => 'réunion d’information','réunion d’échanges' => 'réunion d’échanges','réunion de résolution de problèm' => 'réunion de résolution de problèm'),
                    'required' => false,))
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
        return 'event_form';
    }
}
