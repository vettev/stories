<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Vote
 *
 * @ORM\Table(name="votes")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\VoteRepository")
 */
class Vote
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
    * @ORM\ManyToOne(targetEntity="Post", inversedBy="votes" )
    * @ORM\JoinColumn(name="postId", referencedColumnName="id", onDelete="CASCADE")
    */
    private $post;

    public function getPost()
    {
        return $this->post;
    }

    public function setPost($post)
    {
        $this->post = $post;
        return $this;
    }

    /**
     * @var int
     *
     * @ORM\Column(name="postId", type="integer")
     */
    private $postId;

    /**
    * @ORM\ManyToOne(targetEntity="User", inversedBy="votes" )
    * @ORM\JoinColumn(name="userId", referencedColumnName="id", onDelete="CASCADE")
    */
    private $user;

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @var int
     *
     * @ORM\Column(name="userId", type="integer")
     */
    private $userId;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set postId
     *
     * @param integer $postId
     *
     * @return Vote
     */
    public function setPostId($postId)
    {
        $this->postId = $postId;

        return $this;
    }

    /**
     * Get postId
     *
     * @return int
     */
    public function getPostId()
    {
        return $this->postId;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     *
     * @return Vote
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }
}

