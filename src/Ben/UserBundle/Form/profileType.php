<?php

namespace Ben\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class profileType extends AbstractType
{
    private $diplome;

    public function __construct($config)
    {
        foreach (explode(',', $config['org_diplome']) as $i => $val) 
            $this->diplome[$val] = $val;
        foreach (explode(',', $config['org_expertise']) as $i => $val) 
            $this->expertise[$val] = $val;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('family_name')
            ->add('first_name')
            ->add('birthday', 'date', array('widget' => 'single_text'))
            ->add('gender', 'choice', array('choices' => array('homme' => 'Homme','femme' => 'Femme'),'required' => false,))
            ->add('cin')
            ->add('barcode')
            ->add('address')
            ->add('post_code')
            ->add('city')
            ->add('contry')
            ->add('tel')
            ->add('gsm')
            ->add('job')
            ->add('description')
            ->add('diplome', 'choice', array('choices' => $this->diplome,'required' => false,))
            ->add('expertise', 'choice', array('choices' => $this->expertise,'required' => false,))
            ->add('frequence', 'choice', array('choices' => array('1' => 'mensuel','3' => 'Trimestriel','6' => 'Semestriel','12' => 'Annuel')))
            ->add('method', 'choice', array('choices' => array('Espèces' => 'Espèces','Chèque' => 'Chèque','Virement' => 'Virement')))
            ->add('montant')
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
        return 'user_form_profile';
    }
}
