<?php
namespace App\Service;
use Doctrine\ORM\EntityManagerInterface;

class StatsService{

    private $manager;
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager=$manager;

    }

    public function  getStats(){
        $users=$this->getUsersCount();
        $ads=$this->getAdsCount();
        $bookings=$this->getCountBookings();
        $comments=$this->getCommentsCount();

        return compact('users','ads','bookings','comments');
    }

    public function getUsersCount(){
        return $this->manager->createQuery('select count(u) from App\Entity\User u')->getSingleScalarResult();

    }

    public function getAdsCount(){
        return $this->manager->createQuery('select count(a) from App\Entity\Ad a')->getSingleScalarResult();

    }

    public function getCommentsCount(){

        return $this->manager->createQuery('select count(c) from App\Entity\Comment c')->getSingleScalarResult();
    }

    public function getCountBookings(){
        return $this->manager->createQuery('select count(b) from App\Entity\Booking b')->getSingleScalarResult();
    }

    public function getBestAds(){
        return $this->manager->createQuery('
        SELECT AVG(c.rating) as note,a.title,a.id,u.firstname,u.lastname,u.picture
        FROM App\Entity\Comment c
        JOIN c.ad a
        JOIN a.author u
        GROUP BY a
        ORDER BY note DESC
        ')->setMaxResults(5)
            ->getResult();
    }

    public function getWorseAds(){
        return $this->manager->createQuery('
        SELECT AVG(c.rating) as note,a.title,a.id,u.firstname,u.lastname,u.picture
        FROM App\Entity\Comment c
        JOIN c.ad a
        JOIN a.author u
        GROUP BY a
        ORDER BY note ASC
        ')->setMaxResults(5)
            ->getResult();
    }





}