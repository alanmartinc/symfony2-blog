<?php

namespace Web\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Web\BlogBundle\Entity\Users;
use Web\BlogBundle\Entity\Posts;
use Web\BlogBundle\Form\Type\PostsType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PostsController extends Controller {

    private $session;

    public function __construct() {
        //Cargamos el componente de sesion en todos los metodos
        $this->session = new Session();
    }

    public function indexAction(Request $request) {
        //Entity Manager
        $em = $this->getDoctrine()->getManager();
        $categoryRepository=$em->getRepository("WebBlogBundle:Categories");
        $categories=$categoryRepository->findAll();
        

        return $this->render('WebBlogBundle:Posts:index.html.twig', array(
            "categories"=>$categories
        ));
    }
    
    public function newAction(Request $request){
        //Entity Manager
        $em = $this->getDoctrine()->getManager();
        
        //Categorias
        $categoryRepository=$em->getRepository("WebBlogBundle:Categories");
        $categories=$categoryRepository->findAll();
        
        $categories_array=array();
        foreach($categories as $category){
            $categories_array[$category->getId()]=$category->getName();
        }
        
        
        //instanciamos la entidad Posts
        $post = new Posts();

        //Creamos el formulario, asociado a la entidad
        $form = $this->createForm(new PostsType(), $post);
        
        $form->add('category', 'choice', array(
                "label" => "Categorias: ",
                "attr" => array('class' => 'form-control'),
                'choices' => $categories_array
        ));

        //utilizamos el manejador de peticiones
        $form->handleRequest($request);

        //Si el formulario ha sido enviado
        if ($form->isSubmitted()) {
           
            //Metemos en variables los datos que llegan desde el formulario
            $title = $form->get('title')->getData();
            $description = $form->get('description')->getData();
            $image = $form->get('image')->getData();
            $content = $form->get('content')->getData();
            $category_id = $form->get('category')->getData();
            $category=$categoryRepository->find($category_id);
            $user=$this->get('security.context')->getToken()->getUser();
            
            //Llamamos a los metodos set de la entidad y les metemos los valores del formulario
            $post->setTitle($title);
            $post->setDescription($description);
            $post->setImage($image);
            $post->setContent($content);
            $post->setCategory($category);
            $post->setUser($user);
        }else{
            
        }
        
        //Si el formulario es valido tras aplicar la validacion de la entidad
        if ($form->isValid()) {
            $persist = $em->persist($post);
            $flush = $em->flush();
            
            $this->session->getFlashBag()->add('new', '¡Enhorabuena! Has creado un nuevo post correctamente');
            return $this->redirect($this->generateURL('home'));
        }else{
            if ($form->isSubmitted()) {
                $this->session->getFlashBag()->add('new', 'Rellena correctamente el formulario');
            }
        }
        
        return $this->render('WebBlogBundle:Posts:new.html.twig', array(
            "new_post_form" => $form->createView(),
            "categories"=>$categories
        ));
    }
    
    
}
