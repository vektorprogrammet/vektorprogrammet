<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="thread")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\ThreadRepository")
 */
class Thread
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=250)
     */
    protected $subject;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $datetime;

    /**
     * @ORM\Column(type="string", length=1500)
     */
    protected $text;

    /**
     * @ORM\ManyToOne(targetEntity="Subforum", inversedBy="threads")
     **/
    protected $subforum;

    /**
     * @ORM\OneToMany(targetEntity="Post", mappedBy="thread", cascade={"remove"})
     **/
    protected $posts;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     **/
    protected $user;

    public function __construct()
    {
        $this->posts = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set subject.
     *
     * @param string $subject
     *
     * @return Thread
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject.
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set datetime.
     *
     * @param \DateTime $datetime
     *
     * @return Thread
     */
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;

        return $this;
    }

    /**
     * Get datetime.
     *
     * @return \DateTime
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * Set text.
     *
     * @param string $text
     *
     * @return Thread
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text.
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set subforum.
     *
     * @param \AppBundle\Entity\Subforum $subforum
     *
     * @return Thread
     */
    public function setSubforum(\AppBundle\Entity\Subforum $subforum = null)
    {
        $this->subforum = $subforum;

        return $this;
    }

    /**
     * Get subforum.
     *
     * @return \AppBundle\Entity\Subforum
     */
    public function getSubforum()
    {
        return $this->subforum;
    }

    /**
     * Set user.
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Thread
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Add posts.
     *
     * @param \AppBundle\Entity\Post $posts
     *
     * @return Thread
     */
    public function addPost(\AppBundle\Entity\Post $posts)
    {
        $this->posts[] = $posts;

        return $this;
    }

    /**
     * Remove posts.
     *
     * @param \AppBundle\Entity\Post $posts
     */
    public function removePost(\AppBundle\Entity\Post $posts)
    {
        $this->posts->removeElement($posts);
    }

    /**
     * Get posts.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPosts()
    {
        return $this->posts;
    }

    // Used for unit testing 
    public function fromArray($data = array())
    {
        foreach ($data as $property => $value) {
            $method = "set{$property}";
            $this->$method($value);
        }
    }
}
