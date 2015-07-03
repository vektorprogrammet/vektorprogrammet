<?php

namespace AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class PostRepository extends EntityRepository {
	
	public function findLatestPostBySubforum($subforum){
		
		$post =  $this->getEntityManager()->createQuery("
		
		SELECT p
		FROM AppBundle:Post p
		JOIN p.thread t
		JOIN t.subforum sf
		WHERE sf.id = :subforum
		ORDER BY p.datetime DESC
		")
		->setParameter('subforum', $subforum)
		->setMaxResults(1)
		->getResult();

		return $post;

	}
	
	public function findLatestPostByForum($forum){
		
		$post =  $this->getEntityManager()->createQuery("
		
		SELECT p
		FROM AppBundle:Post p
		JOIN p.thread t
		JOIN t.subforum sf
		JOIN sf.forums f 
		WHERE f.id = :forum
		ORDER BY p.datetime DESC
		")
		->setParameter('forum', $forum)
		->setMaxResults(1)
		->getResult();

		return $post;

	}
	
}
