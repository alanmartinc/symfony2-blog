<?php
namespace Web\BlogBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

//Extendemos al repositorio de entidades y nos creamos un modelo convencional
class PostsRepository extends EntityRepository{
    
    public function getPaginatePosts($pageSize=3,$currentPage){
        $em=$this->getEntityManager();
        
        $posts=$this->findBy(array(), array('id' => 'DESC'));
        
        $dql = "SELECT p FROM Web\BlogBundle\Entity\Posts p ORDER BY p.id DESC";
        $query = $em->createQuery($dql)
                               ->setFirstResult($pageSize * ($currentPage - 1))
                               ->setMaxResults($pageSize);

        $paginator = new Paginator($query, $fetchJoinCollection = true);

        return $paginator;
    }
    
    public function addTags($tags=null,$title=null,$user=null,$category=null,$post=null){
        $em=$this->getEntityManager();
        
        $tagRepository=$em->getRepository("WebBlogBundle:Tags");

        if($post==null){
            $post=$this->findOneBy(array("title"=>$title,"user"=>$user,"category"=>$category));
        }
        
        $tags = explode(",", $tags);

        foreach($tags as $tag){
            $tag_e=$tagRepository->findOneBy(array("name"=>$tag));
            if(count($tag_e)==0){
                $obj_tag=new \Web\BlogBundle\Entity\Tags();
                $obj_tag->setName($tag);
                $em->persist($obj_tag);
                $em->flush();
            }
            $tag=$tagRepository->findOneBy(array("name"=>$tag));  

            $tagsPosts=new \Web\BlogBundle\Entity\TagsPosts();
            $tagsPosts->setPost($post);
            $tagsPosts->setTag($tag);
            $em->persist($tagsPosts);
        }
        $flush=$em->flush();
        
        return $flush;
    }
    
}