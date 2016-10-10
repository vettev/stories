<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Vote;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class VoteController extends Controller
{
    /**
     * @Route("post/{postId}/vote/{sign}", name="vote_new")
     */
    public function newAction($postId, $sign, Request $request)
    {
        if(!$this->isGranted('ROLE_USER'))
        {
            return $this->redirectToRoute('homepage');
        }
        else
        {
            $post = $this->getDoctrine()->getRepository('AppBundle:Post')->findOneById($postId);
            $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneByUsername($this->getUser()->getUsername());
            if(!$post)
            {
                return $this->redirectToRoute('homepage');
            }

            $vote = $this->getDoctrine()->getRepository('AppBundle:Vote')->findOneBy(array('postId' => $post->getId(), 'userId' =>$user->getId()));
            if($vote)
            {
                $disabled = true;
            }
            else
            {
                $vote = new Vote;
                $disabled = false;
            }


            if(!$disabled && $sign)
            {
                $vote->setUser($user);
                $vote->setPost($post);

                $votesQnt = $post->getVotesQnt() + 1;
                $points = $post->getPoints();
                if($sign == 'plus')
                {
                    $points++;
                }
                else if($sign == 'minus')
                {
                    $points--;
                }
                $post->setVotesQnt($votesQnt);
                $post->setPoints($points);
                $em = $this->getDoctrine()->getManager();
                $em->persist($post);
                $em->persist($vote);
                $em->flush();

                return $this->redirectToRoute('vote_new', array('postId' => $postId, 'sign' => $sign));
            }
        }

        return $this->render('vote/add.html.twig',
        [ 'title' => "", 'votes' => $post->getPoints(), 'postId' => $postId, 'disabled' => $disabled]
        );
    }

}
