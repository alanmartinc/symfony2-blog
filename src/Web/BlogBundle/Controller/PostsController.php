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
use Doctrine\ORM\Tools\Pagination\Paginator;

class PostsController extends Controller {

    private $session;

    public function __construct() {
        //Cargamos el componente de sesion en todos los metodos
        $this->session = new Session();
    }

    public function indexAction($page) {
        //Entity Manager
        $em = $this->getDoctrine()->getManager();
        
        //Repositorios de entidades a utilizar
        $categoryRepository=$em->getRepository("WebBlogBundle:Categories");
        $postRepository=$em->getRepository("WebBlogBundle:Posts");
        
        //Conseguir todas las categorías
        $categories=$categoryRepository->findAll();
        
        //Conseguir todos los posts paginados
        $pageSize=3;
        $paginator=$postRepository->getPaginatePosts($pageSize,$page);
        $totalItems = count($paginator);
        $pagesCount = ceil($totalItems / $pageSize);
        
        /*
         * Ejemplo de conseguir todas las 
         * etiquetas asignadaas al post 
         * con relación OneToMany en PHP:
         * 
            foreach($posts as $p){
                $tags=$p->getTagsPosts();
                foreach($tags as $t){
                    var_dump($t->getTag()->getName());
                }
            }
         * 
         */
        
        //Renderizamos la vista
        return $this->render('WebBlogBundle:Posts:index.html.twig', array(
            "categories"=>$categories,
            "posts"     => $paginator,
            "totalItems" => $totalItems,
            "pagesCount" => $pagesCount
        ));
    }
    
    public function newAction(Request $request){
        //Entity Manager
        $em = $this->getDoctrine()->getManager();
        
        //Conseguir todas las categorias
        $categoryRepository=$em->getRepository("WebBlogBundle:Categories");
        $categories=$categoryRepository->findAll();
        
        //instanciamos la entidad Posts
        $post = new Posts();

        //Creamos el formulario, asociado a la entidad
        $form = $this->createForm(new PostsType(), $post);
    
        //utilizamos el manejador de peticiones
        $form->handleRequest($request);

        //Si el formulario ha sido enviado
        if ($form->isSubmitted()) {
           
            //Metemos en variables los datos que llegan desde el formulario
            $title = $form->get('title')->getData();
            $description = $form->get('description')->getData();
            $image = $form->get('image')->getData();
            $content = $form->get('content')->getData();
            
            //Conseguimos el objeto de la categoria
            $category_id = $form->get('category')->getData();
            $category=$categoryRepository->find($category_id);
            
            //Conseguimos el objeto del usuario identificado
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
            
            //Subimos la foto
            $post->upload();
            
            //Persistimos el objeto post
            $persist = $em->persist($post);
            
            //Guardamos en base de datos
            $flush = $em->flush();

            //Añadir tags
            $tags=$form->get('tagsPosts')->getData();
            $postRepository=$em->getRepository("WebBlogBundle:Posts");
            $addTags=$postRepository->addTags($tags,$title,$user,$category);
            
            //Mensaje flash
            if($flush==null && $addTags==null){
                $this->session->getFlashBag()->add('new', '¡Enhorabuena! Has creado un nuevo post correctamente');
            }
            
            //Redirigimos a la Home
            return $this->redirect($this->generateURL('home'));
        }else{
            //Si el formulario está enviado
            if ($form->isSubmitted()) {
                
                //Mensaje flash
                $this->session->getFlashBag()->add('new', 'Rellena correctamente el formulario');
            }
        }
        
        //Renderizamos la vista
        return $this->render('WebBlogBundle:Posts:new.html.twig', array(
            "new_post_form" => $form->createView(),
            "categories"=>$categories,
            "title"=>"Nuevo post"
        ));
    }
    
    public function deleteAction($post){
        //Entity Manager
        $em = $this->getDoctrine()->getManager();
        
        //Repositorios de entidades a utilizar
        $postRepository=$em->getRepository("WebBlogBundle:Posts");
        $tagsPostsRepository=$em->getRepository("WebBlogBundle:TagsPosts");
        
        /*
         * Borrar asociaciones de tags 
         * con el post y borrar post
         */
        $post=$postRepository->findOneBy(array("id"=>$post));
        $tagsposts=$tagsPostsRepository->findBy(array("post"=>$post));
        
        if(count($tagsposts)>=1){
            foreach($tagsposts as $tg){
                $em->remove($tg);
            }
        }
       
        if(count($post)==1){ 
            if($post->getImage()){
                $post->removeUpload();
            }
            $em->remove($post);
        }
        
        $flush=$em->flush();
        
        return $this->redirect($this->generateURL('home'));
    }
    
    
    public function editAction(Request $request,$post){
        //Entity Manager
        $em = $this->getDoctrine()->getManager();
        
        //Repositorios de entidades a utilizar
        $postRepository=$em->getRepository("WebBlogBundle:Posts");
        $categoryRepository=$em->getRepository("WebBlogBundle:Categories");
        
        //Conseguir todas las categorias
        $categories=$categoryRepository->findAll();
        
        //conseguimos el objeto del Post
        $post = $postRepository->findOneBy(array("id"=>$post));
        
        //Cargar foto si existe, hay que pasar el objeto File
        if($post->getWebPath()){
            $file = new \Symfony\Component\HttpFoundation\File\File($post->getWebPath());
            $post->setImage($file);
        }
            
        //Creamos el formulario, asociado a la entidad
        $form = $this->createForm(new PostsType(), $post);
    
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
            
            //Cargar TAGS
            $tags=$post->getTagsPosts();
            if(count($tags)>=1){
                foreach($tags as $tag){
                    $tags_implode[]=$tag->getTag()->getName();
                }

                $tags_comas = implode(",", $tags_implode);

                $form->get('tagsPosts')->setData($tags_comas);
            }
        }
        
        //Si el formulario es valido tras aplicar la validacion de la entidad
        if ($form->isValid()) {
            
            $post->upload();
            $persist = $em->persist($post);
            $flush = $em->flush();
            
            
            $postRepository=$em->getRepository("WebBlogBundle:Posts");
            $tagRepository=$em->getRepository("WebBlogBundle:Tags");
            $tagsPostsRepository=$em->getRepository("WebBlogBundle:TagsPosts");
            
            $post=$postRepository->findOneBy(array("id"=>$post));
            
            //Borrar tags
            $tagsPosts=$tagsPostsRepository->findBy(array("post"=>$post));
            
            foreach($tagsPosts as $tagpost){
                $em->remove($tagpost);
            }
            $em->flush();
            
            //Añadir tags
            $tags=$form->get('tagsPosts')->getData();
            $addTags=$postRepository->addTags($tags,null,null,null,$post);
  
            //Mensaje flash
            $this->session->getFlashBag()->add('new', '¡Enhorabuena! Has editado el post correctamente');
            
            //Redirigir a la home
            return $this->redirect($this->generateURL('home'));
        }else{
            //Si el formulario está enviado
            if ($form->isSubmitted()) {
                
                //Mensaje flash
                $this->session->getFlashBag()->add('new', 'Rellena correctamente el formulario');
            }
        }
        
        //Renderizar vista
        return $this->render('WebBlogBundle:Posts:new.html.twig', array(
            "new_post_form" => $form->createView(),
            "categories"=>$categories,
            "title"=>"Editar post"
        ));
    }
    
}
