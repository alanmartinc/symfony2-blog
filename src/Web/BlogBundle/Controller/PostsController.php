<?php

namespace Web\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Web\BlogBundle\Entity\Users;
use Web\BlogBundle\Form\Type\RegistroType;
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
        

        return $this->render('WebBlogBundle:Posts:index.html.twig');
    }
    
    
  

}
