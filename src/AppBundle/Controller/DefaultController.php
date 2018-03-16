<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\User;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="espace_page")
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function indexAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) 
        {
            return $this->render('home.html.twig');
        
        }

        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) 
        {
            return $this->render('user.html.twig');
        
        }
    }

    /**
    * @Route("/gerant", name="admin_page")
    *
    * @Security("has_role('ROLE_ADMIN')")
    **/
    public function adminPageAction()
    {

        $userManager = $this->get('fos_user.user_manager');
        $users = $userManager->findUsers();


        return $this->render('admin.html.twig', array('users' =>   $users));
    }

    /**
    * @Route("/gerant/details/{id}", name="details_page")
    *
    * @Security("has_role('ROLE_ADMIN')")
    **/
    public function detailsPageAction($id)
    {

        $user = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->find($id);

        return $this->render('details.html.twig', array('user' => $user));
    }

    /**
    * @route("/gerant/update/{id}", name="update_page")
    *
    * @Security("has_role('ROLE_ADMIN')")
    **/
    public function updatePageAction(request $request,$id)
    {
         $user = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->find($id);


        $user->setFirstname($user->getFirstname());
        $user->setLastname($user->getLastname());
        $user->setLastname($user->getUsername());
        $user->setAdresse($user->getAdresse());
        $user->setEmail($user->getEmail());
        $user->setEmail($user->getEmail());
      


        $form =$this->createFormBuilder($user)
            ->add('firstname', TextType::class, array('attr' => array('class' => 'form-control input-lg', 'style' => 'margin-bottom:15px', 'placeholder' => 'First Name')))
            ->add('lastname', TextType::class, array('attr' => array('class' => 'form-control input-lg', 'style' => 'margin-bottom:15px', 'placeholder' => 'Last Name')))
            ->add('username', TextType::class, array('attr' => array('class' => 'form-control input-lg', 'style' => 'margin-bottom:15px', 'placeholder' => 'UserName')))
            ->add('adresse', TextType::class, array('attr' => array('class' => 'form-control input-lg', 'style' => 'margin-bottom:15px', 'placeholder' => 'Adresse')))
            ->add('email', EmailType::class, array('attr' => array('class' => 'form-control input-lg', 'style' => 'margin-bottom:15px', 'placeholder' => 'Email')))
            ->getForm();


        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
           //get data
            $firstname= $form['firstname']->getData();
            $lastname= $form['lastname']->getData();
            $username= $form['username']->getData();
            $adresse= $form['adresse']->getData();
            $email= $form['email']->getData();
            
           

            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('AppBundle:User')->find($id);

            $user->setFirstname($firstname);
            $user->setLastname($lastname);
            $user->setUsername($username);
            $user->setAdresse($adresse);
            $user->setEmail($email);
            


            
            $em->flush();

            $this->addFlash(
                'notice',
                'user bien editer'
            );

            return $this->redirectToRoute('admin_page');

        }

        return $this->render('update.html.twig', array('user' => $user, 'form' => $form->createView()));
    }

     /**
    * @Route("/gerant/add", name="add_page")
    *
    * @Security("has_role('ROLE_ADMIN')")
    **/
    public function createPageAction(Request $request)
    {
        $user =new User;

        $form =$this->createFormBuilder($user)
            ->add('firstname', TextType::class, array('attr' => array('class' => 'form-control input-lg', 'style' => 'margin-bottom:15px', 'placeholder' => 'First Name')))
            ->add('lastname', TextType::class, array('attr' => array('class' => 'form-control input-lg', 'style' => 'margin-bottom:15px', 'placeholder' => 'Last Name')))
            ->add('username', TextType::class, array('attr' => array('class' => 'form-control input-lg', 'style' => 'margin-bottom:15px', 'placeholder' => 'UserName')))
            ->add('adresse', TextType::class, array('attr' => array('class' => 'form-control input-lg', 'style' => 'margin-bottom:15px', 'placeholder' => 'Adresse')))
            ->add('email', EmailType::class, array('attr' => array('class' => 'form-control input-lg', 'style' => 'margin-bottom:15px', 'placeholder' => 'Email')))
            ->add('password', PasswordType::class,  array('attr' => array('class' => 'form-control input-lg', 'style' => 'margin-bottom:15px', 'placeholder' => 'password')))
            ->getForm();


        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
           //get data
            $firstname= $form['firstname']->getData();
            $lastname= $form['lastname']->getData();
            $username= $form['username']->getData();
            $adresse= $form['adresse']->getData();
            $email= $form['email']->getData();
            $pass= $form['password']->getData();

            $user->setFirstname($firstname);
            $user->setLastname($lastname);
            $user->setUsername($username);
            $user->setAdresse($adresse);
            $user->setEmail($email);
            $user->setPassword($pass);

            $em = $this->getDoctrine()->getManager();

            $em->persist($user);
            $em->flush();

            $this->addFlash(
                'notice',
                'user bien ajuter'
            );

            return $this->redirectToRoute('admin_page');

        }
    
        return $this->render('create.html.twig', array('form' => $form->createView()));
    }


     /**
    * @Route("/gerant/remove/{id}", name="delete_page")
    *
    * @Security("has_role('ROLE_ADMIN')")
    **/

    public function removeAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->find($id);

        $em->remove($user);
        $em->flush();

        $this->addFlash(
           'notice',
            'user bien Supprimer'
        );
        
         return $this->redirectToRoute('admin_page');
    }

    /**
    * @Route("/user", name="user_page")
    *
    * @Security("has_role('ROLE_USER')")
    **/
    public function userPageAction()
    {

        return $this->render('user.html.twig');
    }

    /**
    * @Route("/gerant/info", name="info_admin_page")
    *
    * @Security("has_role('ROLE_ADMIN')")
    **/
    public function infoAdminPageAction()
    {

        return $this->render('info_admin.html.twig');
    }


    /**
     * @Route("/home", name="home_page")
     *
     * @Security("has_role('ROLE_USER')")
     */
    public function homeAction(Request $request)
    {
        return $this->render('home.html.twig');
    }

}
