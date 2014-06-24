<?php

namespace Ben\UserBundle\Entity;
 
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
 
class UserRepository extends EntityRepository
{
    public function getActive()
    {
        // Comme vous le voyez, le délais est redondant ici, l'idéale serait de le rendre configurable via votre bundle
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

    public function getUsersBy($searchParam) {
        extract($searchParam);        
        $qb = $this->createQueryBuilder('u')
                ->leftJoin('u.profile', 'p')
                ->addSelect('p')
                ->leftJoin('p.image', 'img')
                ->addSelect('img')
                ->leftJoin('u.groups', 'g')
                ->addSelect('g')
                ->andWhere('u.username like :keyword or u.email like :keyword or u.roles like :keyword or p.city like :keyword')
                ->setParameter('keyword', '%'.$keyword.'%');
        if($group)
            $qb->andWhere('g.id = :group')->setParameter('group', $group);
        if($cin)
            $qb->andWhere('p.cin = :cin')->setParameter('cin', $cin);
        if($gender)
            $qb->andWhere('p.gender = :gender')->setParameter('gender', $gender);
        if($birdDay)
            $qb->andWhere('p.bird_day = :birdDay')->setParameter('birdDay', $birdDay);
        if($city)
            $qb->andWhere('p.city = :city')->setParameter('city', $city);
        if($city)
            $qb->andWhere('p.city = :city')->setParameter('city', $city);
        if($tel)
            $qb->andWhere('p.tel = :tel or p.gsm = :tel')->setParameter('tel', $tel);
        $qb->setFirstResult(($page - 1) * $perPage)
        ->setMaxResults($perPage);

       return new Paginator($qb->getQuery());
    }

    public function findUserById($users)
    {
        $qb = $this->createQueryBuilder('u')
                ->leftJoin('u.profile', 'p')
                ->addSelect('p')
                ->leftJoin('u.reservations', 'r')
                ->addSelect('r')
                ->where('u.id IN (:id)')
                ->setParameter('id', $users)
        ;
 
        return $qb->getQuery()->getResult();
    }

    public function counter() {
        $sql = 'SELECT count(u) FROM ben\UserBundle\Entity\User u';
        $query = $this->_em->createQuery($sql);
         
      return $query->getOneOrNullResult();
    }
}