<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Form\UserType;
use AppBundle\Entity\User;

class UserController extends Controller
{
    /**
     * @Route("/user/manage", name="user_manage")
     */
    public function manageAction(Request $request)
    {
        if(!$this->isGranted('ROLE_ADMIN'))
        {
            return $this->redirectToRoute('homepage');
        }

        return $this->render('user/index.html.twig',
        [ 'title' => "User management",]
        );
    }

    /**
     * @Route("/user/login", name="user_login")
     */
    public function loginAction(Request $request)
    {
        if($this->isGranted('ROLE_USER'))
        {
            return $this->redirectToRoute('homepage');
        }

	    $authenticationUtils = $this->get('security.authentication_utils');

	    // get the login error if there is one
	    $error = $authenticationUtils->getLastAuthenticationError();

	    // last username entered by the user
	    $lastUsername = $authenticationUtils->getLastUsername();

	    return $this->render('user/login.html.twig', array(
	        'last_username' => $lastUsername,
	        'error'         => $error,
	        'title'			=> 'Login',
	    ));
    }

    /**
     * @Route("/user/register", name="user_new")
     */
    public function addAction(Request $request)
    {
        if($this->isGranted('ROLE_USER'))
        {
            return $this->redirectToRoute('homepage');
        }
		// 1) build the form
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {

        	$username = $form['username']->getData();
        	$email = $form['email']->getData();
        	$is_ok = true;

        	$email_check = $this->getDoctrine()
        	->getRepository('AppBundle:User')
        	->findOneByEmail($email);

        	$user_check = $this->getDoctrine()
        	->getRepository('AppBundle:User')
        	->findOneByUsername($username);

        	if($email_check)
        	{
        		$is_ok = false;
        		$this->addFlash('error', 'Email already used.');
        		return $this->redirectToRoute('register');
        	}
        	if($user_check)
        	{
        		$is_ok = false;
        		$this->addFlash('error', 'Username already used.');
        		return $this->redirectToRoute('register');
        	}

        	if($is_ok)
        	{

	            // 3) Encode the password (you could also do this via Doctrine listener)
	            $password = $this->get('security.password_encoder')
	                ->encodePassword($user, $user->getPassword());
	            $user->setPassword($password);
                

	            // 4) save the User!
	            $em = $this->getDoctrine()->getManager();
	            $em->persist($user);
	            $em->flush();

	            // ... do any other work - like sending them an email, etc
	            // maybe set a "flash" success message for the user

	            $this->addFlash(
	                            'notice',
	                            'Registration succesfull!'
	                            );

	            return $this->redirectToRoute('user_login');
        	}
        }

        return $this->render(
            'user/register.html.twig',
            array(
            	'form' 	=> $form->createView(),
            	'title' => 'Registration',
            	)
        );
    }

    /**
     * @Route("/user/logout", name="user_logout")
     */
    public function logoutAction(Request $request)
    {
    	return null;
    }

}
