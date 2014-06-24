<?php

namespace Ben\UserBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class profileRepository extends EntityRepository
{
    public function counter() {
        $sql = ' SELECT count(u) FROM ben\UserBundle\Entity\User u';
        $query = $this->_em->createQuery($sql);
         
      return $query->getOneOrNullResult();
    }
}
