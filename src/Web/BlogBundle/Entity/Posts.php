<?php

namespace Web\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Posts
 */
class Posts {

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $image;

    /**
     * @var string
     */
    private $content;

    /**
     * @var string
     */
    private $status;

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @var \DateTime
     */
    private $time;

    /**
     * @var \Web\BlogBundle\Entity\Categories
     */
    private $category;

    /**
     * @var \Web\BlogBundle\Entity\Users
     */
    private $user;

    /**
     * @var \Web\BlogBundle\Entity\TagsPosts
     */
    protected $tagsPosts;

    public function __construct() {
        $this->tagsPosts = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Posts
     */
    public function setTitle($title) {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Posts
     */
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Set image
     *
     * @param string $image
     * @return Posts
     */
    public function setImage($image) {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string 
     */
    public function getImage() {
        return $this->image;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Posts
     */
    public function setContent($content) {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Posts
     */
    public function setStatus($status) {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Posts
     */
    public function setDate($date) {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate() {
        return $this->date;
    }

    /**
     * Set time
     *
     * @param \DateTime $time
     * @return Posts
     */
    public function setTime($time) {
        $this->time = $time;

        return $this;
    }

    /**
     * Get time
     *
     * @return \DateTime 
     */
    public function getTime() {
        return $this->time;
    }

    /**
     * Set category
     *
     * @param \Web\BlogBundle\Entity\Categories $category
     * @return Posts
     */
    public function setCategory(\Web\BlogBundle\Entity\Categories $category = null) {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \Web\BlogBundle\Entity\Categories 
     */
    public function getCategory() {
        return $this->category;
    }

    /**
     * Set user
     *
     * @param \Web\BlogBundle\Entity\Users $user
     * @return Posts
     */
    public function setUser(\Web\BlogBundle\Entity\Users $user = null) {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Web\BlogBundle\Entity\Users 
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * Add tagsPosts
     *
     * @param  Tag $tag
     * @return Course
     */
    public function addTagsPosts(\Web\BlogBundle\Entity\Tags $tag) {
        $this->tagsPosts[] = $tag;

        return $this;
    }

    /**
     * Get tagsPosts
     *
     * @return ArrayCollection
     */
    public function getTagsPosts() {
        return $this->tagsPosts;
    }

    //SUBIDAS
    public function getAbsolutePath() {
        return null === $this->image ? null : $this->getUploadRootDir() . '/' . $this->image;
    }

    public function getWebPath() {
        return null === $this->image ? null : $this->getUploadDir() . '/' . $this->image;
    }

    public function getUploadRootDir() {
        // la ruta absoluta del directorio donde se deben
        // guardar los archivos cargados
        return __DIR__ . '/../../../../web/' . $this->getUploadDir();
    }

    protected function getUploadDir() {
        // se deshace del __DIR__ para no meter la pata
        // al mostrar el documento/imagen cargada en la vista.
        return 'uploads';
    }

    public function upload() {
        // the file property can be empty if the field is not required
        if (null === $this->getImage()) {
            return;
        }

        // aquí usa el nombre de archivo original pero lo debes
        // sanear al menos para evitar cualquier problema de seguridad
        // move takes the target directory and then the
        // target filename to move to
        $this->getImage()->move(
                $this->getUploadRootDir(), $this->getImage()->getClientOriginalName()
        );

        // set the path property to the filename where you've saved the file
        $this->image = $this->getImage()->getClientOriginalName();

        // limpia la propiedad «file» ya que no la necesitas más
        $this->file = null;
    }

    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) {
            unlink($file);
        }
    }
    
}
