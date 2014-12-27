<?php

namespace Pruebas\EjemploBundle\Entity;

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
     * @var \Pruebas\EjemploBundle\Entity\Tags
     */
    private $tag;

    /**
     * @var \Pruebas\EjemploBundle\Entity\Posts
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
     * @param \Pruebas\EjemploBundle\Entity\Tags $tag
     * @return TagsPosts
     */
    public function setTag(\Pruebas\EjemploBundle\Entity\Tags $tag = null)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get tag
     *
     * @return \Pruebas\EjemploBundle\Entity\Tags 
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Set post
     *
     * @param \Pruebas\EjemploBundle\Entity\Posts $post
     * @return TagsPosts
     */
    public function setPost(\Pruebas\EjemploBundle\Entity\Posts $post = null)
    {
        $this->post = $post;

        return $this;
    }

    /**
     * Get post
     *
     * @return \Pruebas\EjemploBundle\Entity\Posts 
     */
    public function getPost()
    {
        return $this->post;
    }
}
