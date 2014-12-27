<?php

namespace Pruebas\EjemploBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Pruebas\EjemploBundle\Entity\Posts;
use Pruebas\EjemploBundle\Form\PostsType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller {

    public function indexAction($name) {
        return $this->render('PruebasEjemploBundle:Default:index.html.twig', array('name' => $name));
    }

    public function formularioAction(Request $request) {
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
            
            //Llamamos a los metodos set de la entidad y les metemos los valores del formulario
            $post->setTitle($title);
            $post->setDescription($description);
        }
        
        
        
        //Si el formulario es valido tras aplicar la validacion de la entidad
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $persist = $em->persist($post);
            $flush = $em->flush();
            
            echo "Formulario valido !!!";
        }
        
        //Se lo pasamos a la vista
        return $this->render("PruebasEjemploBundle:Default:formulario.html.twig", array(
                    "form" => $form->createView()
        ));
    }

    public function insertarAction($title, $description, $content) {

        /* El objeto deberia llamarse Post pero 
         * al ser generado a partir de una base de datos
         * el objeto se llama como la tabla a la
         * que representa.
         */

        $post = new Posts();

        $post->setTitle($title);
        $post->setDescription($description);
        $post->setContent($content);

        //Entity Manager
        $em = $this->getDoctrine()->getEntityManager();

        //Persistimos en el objeto
        $em->persist($post);

        //Insertarmos en la base de datos
        $flush = $em->flush();

        if ($flush == null) {
            echo "Post creado correctamente";
        } else {
            echo "El post no se ha creado";
        }

        // http://localhost/symfony2/web/app_dev.php/insertar/titulo1/descripcion1/contenido1

        die();
    }

    public function listarAction() {
        //Entity Manager
        $em = $this->getDoctrine()->getEntityManager();

        /*
         * Repositorio de la entidad 
         * (si no creamos uno y le metemos métodos propios 
         * solamente tendrá los métodos de la clase de la entidad)
         */
        $posts = $em->getRepository("PruebasEjemploBundle:Posts");

        //Entity manager y conexión a la BD
        $em = $this->getDoctrine()->getEntityManager();
        $db = $em->getConnection();

        $query = "SELECT * FROM posts; ";
        $stmt = $db->prepare($query);
        $params = array();
        $stmt->execute($params);
        $po = $stmt->fetchAll();

        return $this->render("PruebasEjemploBundle:Default:listar.html.twig", array(
                    "posts" => $po
        ));
    }

    public function actualizarAction($id, $title, $description, $content) {


        //Entity Manager
        $em = $this->getDoctrine()->getEntityManager();
        $posts = $em->getRepository("PruebasEjemploBundle:Posts");


        $post = $posts->find($id);
        $post->setTitle($title);
        $post->setDescription($description);
        $post->setContent($content);

        //Entity Manager
        $em = $this->getDoctrine()->getEntityManager();

        //Persistimos en el objeto
        $em->persist($post);

        //Insertarmos en la base de datos
        $flush = $em->flush();

        if ($flush == null) {
            echo "Post actualizado correctamente";
        } else {
            echo "El post no se ha actualizado";
        }

        // http://localhost/symfony2/web/app_dev.php/actualizar/1/titulo2/descripcion2/contenido2

        die();
    }

    public function borrarAction($id) {


        //Entity Manager
        $em = $this->getDoctrine()->getEntityManager();
        $posts = $em->getRepository("PruebasEjemploBundle:Posts");

        $post = $posts->find($id);
        $em->remove($post);
        $flush = $em->flush();

        if ($flush == null) {
            echo "Post se ha borrado correctamente";
        } else {
            echo "El post no se ha borrado";
        }

        // http://localhost/symfony2/web/app_dev.php/actualizar/1/titulo2/descripcion2/contenido2

        die();
    }

}
