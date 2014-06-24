<?php

namespace Ben\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class profileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('family_name')
            ->add('first_name')
            ->add('bird_day', 'date', array('widget' => 'single_text'))
            ->add('gender', 'choice', array('choices' => array('homme' => 'Homme','femme' => 'Femme'),
                    'required' => false,))
            ->add('address')
            ->add('post_code')
            ->add('city')
            ->add('contry')
            ->add('tel')
            ->add('gsm')
            ->add('job')
            ->add('description')
            ->add('website')
            ->add('facebook')
            ->add('google')
            ->add('twitter')
            ->add('image' , new \Ben\AssociationBundle\Form\imageType())
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ben\UserBundle\Entity\profile'
        ));
    }

    public function getName()
    {
        return 'profiletype';
    }
}
