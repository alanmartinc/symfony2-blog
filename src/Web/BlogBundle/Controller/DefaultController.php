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
        //Instanciamos el obejeto usuario
        $user = new Users();
        
        //Creamos el formulario de Registro
        $registro_form = $this->createForm(new RegistroType(), $user);

        //Recogemos los datos
        $registro_form->handleRequest($request);

        //Si el formulario está enviado
        if ($registro_form->isSubmitted()) {
            //Consigue los datos
            $nombre = $registro_form->get('name')->getData();
            $apellidos = $registro_form->get('surname')->getData();
            $email = $registro_form->get('email')->getData();

            //Cifra la contraseña
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);
            $password = $encoder->encodePassword($registro_form->get('password')->getData(), $user->getSalt());

            //Seteamos los atributos
            $user->setName($nombre);
            $user->setSurname($apellidos);
            $user->setEmail($email);
            $user->setPassword($password);
            $user->setDescription("Describete");
            $user->setImage("default_avatar.png");
            $user->setRole("ROLE_USUARIO");
        }

        //Si el formulario es valido
        if ($registro_form->isValid()) {
            
            $repositorio = $this->getDoctrine()->getRepository('WebBlogBundle:Users');

            $email_existe = false;

            if ($email_existe == false) {
                //Guarda el usuario en base de datos
                $em = $this->getDoctrine()->getManager();
                $persist = $em->persist($user);
                $flush = $em->flush();
                
                //generar flasdata
                $this->session->getFlashBag()->add('info', '¡Enhorabuena! Te has registrado correctamente');
                
                //Redirigir
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

        //Renderizar vista y pasar formulario
        return $this->render('WebBlogBundle:Default:registro.html.twig', 
                array('registro_form' => $registro_form->createView(),
                    "categories"=>$categories
                ));
    }
    
    
    public function loginAction(Request $request){ 
        // Si la autenticación falla que nos lleve a registro y si no a la home
        if($this->session->get(SecurityContext::AUTHENTICATION_ERROR)){
           $this->session->getFlashBag()->add('login', 'Introduce unas credenciales correctas');
           return $this->redirect($this->generateURL('registro'));
        }else{
            if($this->get('security.context')->isGranted('ROLE_USUARIO')){
                return $this->redirect($this->generateURL('home'));
            }
        }
    }

}
