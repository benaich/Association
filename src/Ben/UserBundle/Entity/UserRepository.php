<?php

namespace Ben\UserBundle\Entity;
 
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
 
class UserRepository extends EntityRepository
{
    /* advanced search */
    public function search($searchParam) {
        // var_dump($searchParam);die;
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
        if(!empty($ids))
            $qb->andWhere('u.id in (:ids)')->setParameter('ids', $ids);
        if(!empty($cin))
            $qb->andWhere('p.cin = :cin')->setParameter('cin', $cin);
        if(!empty($barcode))
            $qb->andWhere('p.barcode = :barcode')->setParameter('barcode', $barcode);
        if(!empty($gender))
            $qb->andWhere('p.gender = :gender')->setParameter('gender', $gender);
            if(!empty($group))
                $qb->andWhere('g.id = :group')->setParameter('group', $group);
        if(!empty($date_from))
            $qb->andWhere('p.birthday > :date_from')->setParameter('date_from', $date_from);
        if(!empty($date_to))
            $qb->andWhere('p.birthday < :date_to')->setParameter('date_to', $date_to);
        if(!empty($status))
            $qb->andWhere('status.id = :status')->setParameter('status', $status);
        if(!empty($city))
            $qb->andWhere('p.city = :city')->setParameter('city', $city);
        if(isset($archive))
            $qb->andWhere('p.archived = :archive')->setParameter('archive', $archive);
        if(!empty($cotisation)){            
            $qb2 = $this->_em->createQueryBuilder()
                ->select('usr.id')
                ->from('Ben\UserBundle\Entity\User', 'usr')
                ->leftJoin('usr.cotisations', 'c')
                ->groupBy('usr.id')
                ->having('DATE_DIFF(max(c.date_to), CURRENT_DATE()) >= 0');
            if($cotisation == 1)
                $qb->andWhere($qb->expr()->in('u.id', $qb2->getDQL()));
            else $qb->andWhere($qb->expr()->notIn('u.id', $qb2->getDQL()));
        }

        if(!empty($sortBy)){
            $sortBy = ($sortBy == 'familyname') ? 'family_name' : $sortBy;
            $sortBy = ($sortBy == 'firstname') ? 'first_name' : $sortBy;
            $sortBy = in_array($sortBy, array('first_name', 'family_name', 'birthday')) ? $sortBy : 'id';
            $sortDir = ($sortDir == 'DESC') ? 'DESC' : 'ASC';
            $qb->orderBy('p.' . $sortBy, $sortDir);
        }
        if(!empty($perPage)) $qb->setFirstResult(($page - 1) * $perPage)->setMaxResults($perPage);
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
                ->leftJoin('u.cotisations', 'c')
                ->addSelect('c')
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

    public function counter($archive = 0, $searchParam = NULL) {
        $qb = $this->createQueryBuilder('u')->select('COUNT(u)')
                ->leftJoin('u.profile', 'p')
                ->andWhere('p.archived = :archive')->setParameter('archive', $archive);
        if(isset($searchParam)){
            extract($searchParam);
            if(!empty($group))
                $qb->leftJoin('u.groups', 'g')->andWhere('g.id = :group')->setParameter('group', $group);
            if(!empty($status))
                $qb->leftJoin('u.avancements', 'av')->leftJoin('av.status', 'status')->andWhere('status.id = :status')->setParameter('status', $status);
            if(!empty($city))
                $qb->andWhere('p.city = :city')->setParameter('city', $city);
            if(!empty($gender))
                $qb->andWhere('p.gender = :gender')->setParameter('gender', $gender);
            if(!empty($age)){
                $age = explode("-",$age);
                $qb->andWhere('DATEDIFF(Cast(CURRENT_DATE() as Date), Cast(p.birthday as Date)) between :from and :to ')
                ->setParameter('from', $age[0] * 365)
                ->setParameter('to', $age[1] * 365);
            }
        }
        return $qb->getQuery()->getSingleScalarResult();
    }

    public function statsByStatus()
    {
        return  $this->fetch('select s.name as label, count(*) as data from user u
            left join avancement a on a.user_id = u.id
            left join status s on s.id = a.status_id
            group by s.id');
    }

    public function statsByCity()
    {
        return  $this->fetch('select city as label, count(*) as data from profile p group by city');
    }

    public function statsByGender()
    {
        return  $this->fetch('select gender as label, count(*) as data from profile group by gender');
    }

    public function statsByCotisation()
    {
        return  $this->fetch('select * from 
            (select count(*) as yes from (select u.id from user u  LEFT JOIN cotisation c on c.user_id = u.id  group by user_id having DATEDIFF(max(c.date_to), CURRENT_DATE()) >= 0) A) A,
            (select count(*) as no from (select u.id from user u  LEFT JOIN cotisation c on c.user_id = u.id  group by user_id having DATEDIFF(max(c.date_to), CURRENT_DATE()) < 0) A) B,
            (select count(*) as never from user u left join cotisation c on c.user_id = u.id where c.id is NULL) C')[0];
    }

    public function statsByCreated()
    {
        return  $this->fetch('select DATE(created) as x ,COUNT(id) as y from user group by x order by x');
    }

    public function statsByAge()
    {
        return  $this->fetch('select DATE(created) as x ,sum(price) as y from cotisation group by x order by x');
    }

    public function statsByRevenu()
    {
        return  $this->fetch('select DATE(created) as x ,sum(price) as y from cotisation group by x order by x');
    }

    private function fetch($query)
    {
        $stmt = $this->getEntityManager()->getConnection()->prepare($query);
        $stmt->execute();
        return  $stmt->fetchAll();
    }
}