<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Post
 *
 * @ORM\Table(name="post")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PostRepository")
 */
class Post
{

    /**
    * @ORM\ManyToOne(targetEntity="User", inversedBy="posts")
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
    * @ORM\OneToMany(targetEntity="Comment", mappedBy="post")
    * @ORM\OrderBy({"createdAt" = "DESC"})
    */
    protected $comments;

    public function getComments()
    {
        return $this->comments;
    }

    /**
    * @ORM\OneToMany(targetEntity="Vote", mappedBy="post")
    */
    protected $votes;

    public function getVotes()
    {
        return $this->votes;
    }

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->votes = new ArrayCollection();
    }

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="userId", type="integer")
     */
    private $userId;

    /**
     * @var int
     *
     * @ORM\Column(name="votesQnt", type="integer")
     */
    private $votesQnt;

    /**
     * @var int
     *
     * @ORM\Column(name="points", type="integer")
     */
    private $points;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @var bool
     *
     * @ORM\Column(name="isWaiting", type="boolean")
     */
    private $isWaiting;




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
     * Set userId
     *
     * @param integer $userId
     *
     * @return Post
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

    /**
     * Set votes
     *
     * @param integer $votes
     *
     * @return Post
     */
    public function setVotesQnt($votesQnt)
    {
        $this->votesQnt = $votesQnt;

        return $this;
    }

    /**
     * Get votes
     *
     * @return int
     */
    public function getVotesQnt()
    {
        return $this->votesQnt;
    }

    /**
     * Set points
     *
     * @param integer $points
     *
     * @return Post
     */
    public function setPoints($points)
    {
        $this->points = $points;

        return $this;
    }

    /**
     * Get points
     *
     * @return int
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Post
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Post
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getIsWaiting()
    {
        return $this->isWaiting;
    }

    public function setIsWaiting($isWaiting)
    {
        $this->isWaiting = $isWaiting;
        return $this;
    }

}

