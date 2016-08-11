<?php
/**
 * Created by PhpStorm.
 * User: Tommy
 * Date: 06.05.2015
 * Time: 17:49.
 */

namespace AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class PhotoRepository extends EntityRepository
{
    /**
     * Returns all photos requesting user is allowed to see.
     */
    public function findViewablePhotos()
    {
        //todo: implement if needed else delete
    }

    /**
     * Returns all photos requesting user is allowed to edit/delete.
     */
    public function findEditablePhotos()
    {
        //todo: implement if needed else delete
    }
}
