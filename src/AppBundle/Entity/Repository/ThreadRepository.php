<?php

namespace AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class ThreadRepository extends EntityRepository {

	public function findLatestThreadBySubforum($subforum){
		
		$thread =  $this->getEntityManager()->createQuery("
		
		SELECT t
		FROM AppBundle:Thread t 
		WHERE t.subforum = :subforum
		ORDER BY t.datetime DESC
		")
		->setParameter('subforum', $subforum)
		->setMaxResults(1)
		->getResult();

		return $thread;

	}
	
	public function findLatestThreadByForum($forum){
		
		$thread =  $this->getEntityManager()->createQuery("
		
		SELECT t
		FROM AppBundle:Thread t 
		JOIN t.subforum sf
		JOIN sf.forums f 
		WHERE f.id = :forum
		ORDER BY t.datetime DESC
		")
		->setParameter('forum', $forum)
		->setMaxResults(1)
		->getResult();

		return $thread;

	}

	
}
