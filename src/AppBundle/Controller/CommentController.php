<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Comment;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CommentController extends Controller
{
    /**
     * @Route("post/comment/{postId}", name="comment_new")
     */
  public function newAction($postId, Request $request)
  {
    if(!$this->isGranted('ROLE_USER'))
    {
      return $this->redirectToRoute('homepage');
    }
    else
    {
       $post = $this->getDoctrine()->getRepository('AppBundle:Post')->findOneById($postId);
      if(!$post)
      {
        $this->redirectToRoute('homepage');
      }
      $comment = new Comment;
      $actualUser = $this->getUser()->getUsername();
      $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneByUsername($actualUser);
      $target = 'post-' . $postId;
      $date = new \Datetime();

      $form = $this->createFormBuilder($comment, array( 'attr' => array('class' => 'ajax-form comment-form-add', 'data-target' => $target) ))
      ->add('content', TextareaType::class,  array('attr' => array('class' => 'form-control'), 'label' => "Content of your comment" ))
        ->add('Add comment', SubmitType::class, array('attr' => array('class' => 'btn btn-info form-control')))
        ->getForm();

      $form->handleRequest($request);

      if($form->isSubmitted() && $form->isValid())
      {
        $content = $form['content']->getData();

        $comment->setUser($user);
        $comment->setPost($post);
        $comment->setCreatedAt($date);
        $comment->setContent($content);

        $em = $this->getDoctrine()->getManager();
        $em->persist($comment);
        $em->flush();

        return $this->redirectToRoute('comment_show', array('id' => $comment->getId()));
      }
    }

    return $this->render('comment/new.html.twig',
    [ 
    'form'    => $form->createView(),
    'postId'  => $postId,
    ]);
  }

    /**
     * @Route("/comment/remove/{id}", name="comment_remove")
     */
  public function removeAction($id, Request $request)
  {
    $comment = $this->getDoctrine()->getRepository('AppBundle:Comment')->findOneById($id);
    if(!$this->isGranted('ROLE_ADMIN') && !($this->getUser()->getUsername() == $comment->getUser()->getUsername() ) )
    {
      return $this->redirectToRoute('homepage');
    }

    $postId = $comment->getPostId();
    if(!$comment)
    {
      return $this->redirectToRoute('homepage');
    }

    $em = $this->getDoctrine()->getManager();
    $em->remove($comment);
    $em->flush();

    $this->addFlash('notice', 'Comment has been removed');
    return $this->redirectToRoute('post_show', array('id' => $postId));
  }

    /**
     * @Route("/comment/show/{id}", name="comment_show")
     */
  public function showAction($id, Request $request)
  {

    $comment = $this->getDoctrine()->getRepository('AppBundle:Comment')->findOneById($id);

    return $this->render('comment/comment.html.twig',
    [ 
    'comment'    => $comment,
    ]);
  }

    /**
     * @Route("comment/edit/{id}", name="comment_edit")
     */
  public function editAction($id, Request $request)
  {
    $comment = $this->getDoctrine()->getRepository('AppBundle:Comment')->findOneById($id);

    if(!$this->isGranted('ROLE_ADMIN') && !($this->getUser()->getUsername() == $comment->getUser()->getUsername() ) )
    {
      return $this->redirectToRoute('homepage');
    }
    else
    {
      $target = 'comment-' . $comment->getId();
      $date = new \Datetime();

      $form = $this->createFormBuilder($comment, array( 'attr' => array('class' => 'ajax-form comment-form-edit', 'data-target' => $target) ))
      ->add('content', TextareaType::class,  array('attr' => array('class' => 'form-control'), 'label' => "Content of your comment" ))
        ->add('Edit comment', SubmitType::class, array('attr' => array('class' => 'btn btn-info form-control')))
        ->getForm();

      $form->handleRequest($request);

      if($form->isSubmitted() && $form->isValid())
      {
        $content = $form['content']->getData();

        $comment->setUser($comment->getUser());
        $comment->setPost($comment->getPost());
        $comment->setCreatedAt($date);
        $comment->setContent($content);

        $em = $this->getDoctrine()->getManager();
        $em->persist($comment);
        $em->flush();

        return $this->redirectToRoute('comment_show', array('id' => $comment->getId()));
      }
    }

    return $this->render('comment/edit.html.twig',
    [ 
    'form'    => $form->createView(),
    'id'  => $id,
    ]);
  }     

}