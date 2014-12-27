<?php

namespace Web\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Web\BlogBundle\Entity\Users;
use Web\BlogBundle\Form\Type\RegistroType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class DefaultController extends Controller {

    private $session;

    public function __construct() {
        //Cargamos el componente de sesion en todos los metodos
        $this->session = new Session();
    }

    public function indexAction(Request $request) {
        $user = new Users();
        $registro_form = $this->createForm(new RegistroType(), $user);

        $registro_form->handleRequest($request);

        if ($registro_form->isSubmitted()) {
            $nombre = $registro_form->get('name')->getData();
            $apellidos = $registro_form->get('surname')->getData();
            $email = $registro_form->get('email')->getData();

            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);
            $password = $encoder->encodePassword($registro_form->get('password')->getData(), $user->getSalt());

            $user->setName($nombre);
            $user->setSurname($apellidos);
            $user->setEmail($email);
            $user->setPassword($password);
            $user->setDescription("Describete");
            $user->setImage("default_avatar.png");
            $user->setRole("ROLE_USUARIO");
        }

        if ($registro_form->isValid()) {
            /* Conseguimos o instanciamos el repositorio de entidades de la 
             * tabla users que nos da doctrine
             * tememos UsuariosRepository.php que actua de modelo convencional
             */
            $repositorio = $this->getDoctrine()->getRepository('WebBlogBundle:Users');

            // $email_existe=$repositorio->ComprobarExistencia("Email",$email);
            $email_existe = false;

            if ($email_existe == false) {
                $em = $this->getDoctrine()->getManager();
                $persist = $em->persist($user);
                $flush = $em->flush();
                //generar flasdata
                $this->session->getFlashBag()->add('info', '¡Enhorabuena! Te has registrado correctamente');
                return $this->redirect($this->generateURL('registro'));
            } else {
                //genera una sesion flasdata
                $this->session->getFlashBag()->add('info', 'Email o nick duplicado intentalo de nuevo');
            }
        }

        //Categorias
        $em = $this->getDoctrine()->getEntityManager();
        $categoryRepository=$em->getRepository("WebBlogBundle:Categories");
        $categories=$categoryRepository->findAll();

        return $this->render('WebBlogBundle:Default:registro.html.twig', 
                array('registro_form' => $registro_form->createView(),
                    "categories"=>$categories
                ));
    }
    
    
    public function loginAction(Request $request){ 
        if($this->session->get(SecurityContext::AUTHENTICATION_ERROR)){
           $this->session->getFlashBag()->add('login', 'Introduce unas credenciales correctas');
           return $this->redirect($this->generateURL('registro'));
        }else{
            if($this->get('security.context')->isGranted('ROLE_USUARIO')){
                return $this->redirect($this->generateURL('inicio'));
            }
        }
    }

}
