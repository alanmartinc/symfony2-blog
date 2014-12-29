<?php

namespace Web\BlogBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PostsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title','text', array("label" => "Titulo: ",
                    "required" => false,
                    "attr" => array('class' => 'form-control')))
                
            ->add('description','textarea', array("label" => "Descripción: ",
                    "required" => false,
                    "attr" => array('class' => 'form-control')))
                
            ->add('category', 'entity', array(
                "label" => "Categoria: ",
                'class' => 'WebBlogBundle:Categories',
                "attr" => array('class' => 'form-control'),
                "property"=>"name"
            ))
                
            ->add('image','file', array("label" => "Imagen: ",
                    "required" => false,
                    "attr" => array('class' => '')))
                
            ->add('content','textarea', array("label" => "Contenido: ",
                    "required" => false,
                    "attr" => array('class' => 'form-control')))
            
            ->add('tagsPosts', 'text', array(
                "label" => "Etiquetas: ",
                'mapped'=>false,  
                "attr" => array('class' => 'form-control'),

            ))
                
            ->add('Guardar', 'submit', array("attr" => array('class' => 'btn btn-success')));
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Web\BlogBundle\Entity\Posts'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'web_blogbundle_posts';
    }
}
