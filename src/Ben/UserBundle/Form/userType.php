<?php

namespace Ben\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class userType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('email')
            ->add('plainpassword', 'text', array('required' => false))
            ->add('enabled', 'checkbox', array('required' => false))
            ->add('profile' , new profileType())
            ->add('groups', null, array('expanded' => "true", "multiple" => "true"))
            ->add('status')
            ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ben\UserBundle\Entity\user'
        ));
    }

    public function getName()
    {
        return 'user_type';
    }
}