<?php

namespace Web\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TagsPosts
 */
class TagsPosts
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Web\BlogBundle\Entity\Tags
     */
    private $tag;

    /**
     * @var \Web\BlogBundle\Entity\Posts
     */
    private $post;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set tag
     *
     * @param \Web\BlogBundle\Entity\Tags $tag
     * @return TagsPosts
     */
    public function setTag(\Web\BlogBundle\Entity\Tags $tag = null)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get tag
     *
     * @return \Web\BlogBundle\Entity\Tags 
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Set post
     *
     * @param \Web\BlogBundle\Entity\Posts $post
     * @return TagsPosts
     */
    public function setPost(\Web\BlogBundle\Entity\Posts $post = null)
    {
        $this->post = $post;

        return $this;
    }

    /**
     * Get post
     *
     * @return \Web\BlogBundle\Entity\Posts 
     */
    public function getPost()
    {
        return $this->post;
    }
}
