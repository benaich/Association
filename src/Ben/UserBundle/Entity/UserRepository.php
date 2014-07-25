<?php

namespace Ben\UserBundle\Entity;
 
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
 
class UserRepository extends EntityRepository
{
    /* advanced search */
    public function search($searchParam) {
        extract($searchParam);        
        $qb = $this->createQueryBuilder('u')
                ->leftJoin('u.profile', 'p')
                ->addSelect('p')
                ->leftJoin('p.image', 'img')
                ->addSelect('img')
                ->leftJoin('u.groups', 'g')
                ->addSelect('g')
                ->leftJoin('u.avancements', 'av')
                ->addSelect('av')
                ->leftJoin('av.status', 'status');

        if(!empty($keyword))
            $qb->andWhere('concat(p.family_name, p.first_name) like :keyword or u.username like :keyword or u.email like :keyword or u.roles like :keyword or p.city like :keyword')
                ->setParameter('keyword', '%'.$keyword.'%');
        if(!empty($group))
            $qb->andWhere('g.id = :group')->setParameter('group', $group);
        if(!empty($cin))
            $qb->andWhere('p.cin = :cin')->setParameter('cin', $cin);
        if(!empty($barcode))
            $qb->andWhere('p.barcode = :barcode')->setParameter('barcode', $barcode);
        if(!empty($gender))
            $qb->andWhere('p.gender = :gender')->setParameter('gender', $gender);
        if(!empty($status))
            $qb->andWhere('status.id = :status')->setParameter('status', $status);
        if(!empty($date_from))
            $qb->andWhere('p.birthday > :date_from')->setParameter('date_from', $date_from);
        if(!empty($date_to))
            $qb->andWhere('p.birthday < :date_to')->setParameter('date_to', $date_to);
        if(!empty($city))
            $qb->andWhere('p.city = :city')->setParameter('city', $city);
        if(!empty($cotisation)){
            $current=new \DateTime("now");
            $current->setDate(DATE_FORMAT($current, 'Y'),01,01);
            if($cotisation==1)
                $qb->leftJoin('u.cotisations', 'c')
                    // ->andWhere('c.date_from > :current')->setParameter('current', $current)
                    ->andWhere($qb->expr()->andx($qb->expr()->isNotNull('c.user')));
            else $qb->leftJoin('u.cotisations', 'c')
                    // ->andWhere('c.date_from < :current')->setParameter('current', $current)
                    ->andWhere($qb->expr()->andx($qb->expr()->isNull('c.user')));
        }

        if(!empty($sortBy)){
            $sortBy = ($sortBy == 'familyname') ? 'family_name' : $sortBy;
            $sortBy = ($sortBy == 'firstname') ? 'first_name' : $sortBy;
            $sortBy = in_array($sortBy, array('first_name', 'family_name', 'birthday')) ? $sortBy : 'id';
            $sortDir = ($sortDir == 'DESC') ? 'DESC' : 'ASC';
            $qb->orderBy('p.' . $sortBy, $sortDir);
        }
        if(!empty($perPage)) $qb->setFirstResult(($page - 1) * $perPage)->setMaxResults($perPage);
        // var_dump($qb->getQuery()->getDql());die;
       return new Paginator($qb->getQuery());
    }


    public function getUsersByEvent($id, $obj=false) {       
       $qb = $this->createQueryBuilder('u')
                ->leftJoin('u.profile', 'p')
                ->addSelect('p')
                ->leftJoin('u.groups', 'g')
                ->leftJoin('g.events', 'e')
                ->andWhere('e.id = :id')
                ->setParameter('id', $id);
        if($obj) return $qb->getQuery()->getResult();
       return $qb->getQuery()->getArrayResult();
    }
    public function getActive()
    {
        $delay = new \DateTime();
        $delay->setTimestamp(strtotime('2 minutes ago'));
 
        $qb = $this->createQueryBuilder('u')
            ->where('u.lastActivity > :delay')
            ->setParameter('delay', $delay)
        ;
 
        return $qb->getQuery()->getResult();
    }
    
    public function getUsers()
    {
        $qb = $this->createQueryBuilder('u')
                ->leftJoin('u.profile', 'profile')
                ->addSelect('profile')
        ;
 
        return $qb->getQuery()->getResult();
    }

    public function findUser($id)
    {
        $qb = $this->createQueryBuilder('u')
                ->leftJoin('u.profile', 'p')
                ->addSelect('p')
                ->leftJoin('p.image', 'img')
                ->addSelect('img')
                ->leftJoin('u.groups', 'g')
                ->addSelect('g')
                ->leftJoin('u.reservations', 'r')
                ->addSelect('r')
                ->where('u.id = :id')
                ->setParameter('id', $id)
        ;
 
        return $qb->getQuery()->getOneOrNullResult();
    }
    
    public function autocomplete($keyword) {
        $qb = $this->createQueryBuilder('u')
                ->Where('u.username like :keyword')
                ->setParameter('keyword', '%' . $keyword . '%')
                ->setMaxResults(10);

        return $qb->getQuery()->getResult();
    }

    public function findUserById($users = null)
    {
        $qb = $this->createQueryBuilder('u')
                ->leftJoin('u.profile', 'p')
                ->addSelect('p')
                ->leftJoin('p.image', 'img')
                ->addSelect('img');
        if(!empty($users))
            $qb->where('u.id IN (:id)')->setParameter('id', $users);
 
        return $qb->getQuery()->getResult();
    }

    public function counter() {
        $qb = $this->createQueryBuilder('u')->select('COUNT(u)');
        return $qb->getQuery()->getSingleScalarResult();
    }
}