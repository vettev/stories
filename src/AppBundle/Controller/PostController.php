<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Post;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PostController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
    	$em = $this->getDoctrine()->getManager();

    	$query = $em->CreateQuery
    	(
    		'SELECT p FROM AppBundle:Post p
    		WHERE p.isWaiting = 0
    		ORDER BY p.createdAt DESC'
    	);
    	$posts = $query->getResult();

        return $this->render('post/index.html.twig',
        [ 'title' => "Stories", 'posts' => $posts]
        );
    }

    /**
     * @Route("/post/new", name="post_new")
     */
    public function addAction(Request $request)
    {
    	
    	if(!$this->isGranted('ROLE_USER'))
    	{
    		$this->addFlash('error', "You must be logged in to post story.");
    		return $this->redirectToRoute('user_login');
    	}
    	$post = new Post();
    	$form = $this->createFormBuilder($post)
    	->add('content', TextareaType::class,  array('attr' => array('class' => 'form-control', 'rows' => 15), 'label' => "Content of your story" ))
    	->add('Add story', SubmitType::class, array('attr' => array('class' => 'btn btn-info form-control')))
    	->getForm();

    	$form->handleRequest($request);

    	if($form->isSubmitted() && $form->isValid())
    	{

	    	$actualUser = $this->getUser()->getUsername();
	    	$user = $this->getDoctrine()
	    	->getRepository('AppBundle:User')
	    	->findOneByUsername($actualUser);

	    	$date = new \Datetime();
	    	$content = $form['content']->getData();

	    	$post->setUser($user);
	    	$post->setVotesQnt(0);
	    	$post->setPoints(0);
	    	$post->setCreatedAt($date);
	    	$post->setIsWaiting(1);


	    	$em = $this->getDoctrine()->getManager();
	    	$em->persist($post);
	    	$em->flush();

	    	$this->addFlash('notice', 'Your story has been posted.');
	    	return $this->redirectToRoute('post_wait');
	    }

        return $this->render('post/new.html.twig',
        [ 'title' => "Add Story", 'form' => $form->createView(),]
        );
    }

    /**
     * @Route("/post/wait", name="post_wait")
     */
    public function waitAction(Request $request)
    {
    	$em = $this->getDoctrine()->getManager();

    	$query = $em->CreateQuery
    	(
    		'SELECT p FROM AppBundle:Post p
    		WHERE p.isWaiting = 1
    		ORDER BY p.createdAt DESC'
    	);
    	$posts = $query->getResult();

        return $this->render('post/wait.html.twig',
        [ 'title' => "Stories waiting room", 'posts' => $posts]
        );
    }

    /**
     * @Route("/post/manage", name="post_manage")
     */
    public function manageAction(Request $request)
    {
    	if(!$this->isGranted('ROLE_ADMIN'))
    	{
    		return $this->redirectToRoute('homepage');
    	}

    	$posts = $this->getDoctrine()->
    	getRepository('AppBundle:Post')
    	->findAll();

        return $this->render('post/manage.html.twig',
        [ 'title' => "Stories management", 'posts' => $posts]
        );
    }

    /**
     * @Route("/post/move/{id}", name="post_move")
     */
    public function moveAction($id, Request $request)
    {
    	if(!$this->isGranted('ROLE_ADMIN'))
    	{
    		return $this->redirectToRoute('homepage');
    	}

    	$post = $this->getDoctrine()->
    	getRepository('AppBundle:Post')
    	->findOneById($id);

    	if(!$post)
    	{
    		$this->redirectToRoute('homepage');
    	}
    	else
    	{
    		$post->setIsWaiting(0);
    		$em = $this->getDoctrine()->getManager();

    		$em->persist($post);
    		$em->flush();

    		$this->addFlash('notice', "Post successfully moved.");
    		return $this->redirectToRoute('homepage');
    	}
    }

    /**
     * @Route("/post/remove/{id}", name="post_remove")
     */
    public function removeAction($id, Request $request)
    {
    	if(!$this->isGranted('ROLE_ADMIN'))
    	{
    		return $this->redirectToRoute('homepage');
    	}

    	$post = $this->getDoctrine()->
    	getRepository('AppBundle:Post')
    	->findOneById($id);

    	if(!$post)
    	{
    		$this->redirectToRoute('homepage');
    	}
    	else
    	{
    		$em = $this->getDoctrine()->getManager();

    		$em->remove($post);
    		$em->flush();

    		return $this->redirectToRoute('homepage');
    	}
    }

    /**
     * @Route("/post/edit/{id}", name="post_edit")
     */
    public function editAction($id, Request $request)
    {
    	if(!$this->isGranted('ROLE_ADMIN'))
    	{
    		return $this->redirectToRoute('homepage');
    	}

    	$post = $this->getDoctrine()->
    	getRepository('AppBundle:Post')
    	->findOneById($id);

    	if(!$post)
    	{
    		$this->redirectToRoute('homepage');
    	}
    	else
    	{
            $target = "post-" . $id;
	    	$form = $this->createFormBuilder($post, array( 'attr' => array('class' => 'ajax-form post-form', 'data-target' => $target) ))
	    	->add('content', TextareaType::class,  array('attr' => array('class' => 'form-control', 'rows' => 15,), 'label' => "Content of your story" ))
	    	->add('Edit', SubmitType::class, array('attr' => array('class' => 'btn btn-info form-control')))
	    	->getForm();

    		$form->handleRequest($request);

    		if($form->isSubmitted() && $form->isValid())
    		{
	    		$content = $form['content']->getData();
	    		$post->setContent($content);

	    		$em = $this->getDoctrine()->getManager();
	    		$em->persist($post);
	    		$em->flush();
                
	    		return $this->redirectToRoute('post_content', array('id' => $id));
            }
    	}

    	return $this->render('post/edit.html.twig',
        [ 'title' => "Edit Story", 'form' => $form->createView(), 'id' => $id]
        );
    }

    /**
     * @Route("/post/show/{id}", name="post_show")
     */
    public function showAction($id, Request $request)
    {
        $post = $this->getDoctrine()->getRepository('AppBundle:Post')->findOneById($id);

        if(!$post)
        {
            $this->redirectToRoute('homepage');
        }

        return $this->render('post/show.html.twig',
        [ 'title' => "Story ".$id, 'post' => $post ]
        );
    }

    /**
     * @Route("/post/content/{id}", name="post_content")
     */
    public function contentAction($id, Request $request)
    {
        $post = $this->getDoctrine()->getRepository('AppBundle:Post')->findOneById($id);

        if(!$post)
        {
            $this->redirectToRoute('homepage');
        }

        return $this->render('post/content.html.twig',
        ['post' => $post ]
        );
    }

}
