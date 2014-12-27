<?php

namespace Pruebas\EjemploBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class PostsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title','text',array("required"=>false,
                                                 'attr' => array('class'=>'form-control')
                                            ))
            ->add('description','textarea',array("required"=>false,
                                                 'attr' => array('class'=>'form-control')
                                            ))
            ->add('Guardar',"submit",array('attr' => array('class'=>'btn btn-success')))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Pruebas\EjemploBundle\Entity\Posts'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'pruebas_ejemplobundle_posts';
    }
}
