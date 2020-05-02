<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use App\Entity\Image;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

    private $encoder;
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder=$encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker=Factory::create('fr-FR');
        $users=[];
        $genres=['men','women'];
        for($i=1;$i<=10;$i++){
            $user=new User();

            $genre=$faker->randomElement($genres);

            $pictureId=mt_rand(1,99).'.jpg';
            $picture="https://randomuser.me/api/portraits/$genre/$pictureId";
            $hash=$this->encoder->encodePassword($user,'password');
            $user->setFirstname($faker->firstName($genres))
                ->setLastname($faker->lastName)
                ->setEmail($faker->email)
                ->setPicture($picture)
                ->setIntroduction($faker->sentence())
                ->setDescription("<p>".join('</p><p>',$faker->paragraphs(5))."</p>")
                ->setHash('password');
            $manager->persist($user);
            $users[]=$user;


        }


        for($i=1;$i<=30;$i++){

        $ad           = new Ad();
        $title        = $faker->sentence(5);
        $coverImage   = $faker->imageUrl(1000,350);
        $introduction = $faker->paragraph(2);
        $content      = "<p>".join('</p><p>',$faker->paragraphs(5))."</p>";
        $user=$users[mt_rand(0,count($users)-1)];
        $ad->setTitle($title)
            ->setCoverImage($coverImage)
            ->setIntroduction($introduction)
            ->setContent($content)
            ->setPrice(mt_rand(40,200))
            ->setRooms(mt_rand(1,5))
            ->setAuthor($user)
        ;

            for($j=1;$j<= mt_rand(2,5);$j++){
                $image=new Image();
                $image->setUrl($faker->imageUrl())
                    ->setCaption($faker->sentence())
                    ->setAd($ad);
                $manager->persist($image);

            }

            $manager->persist($ad);

        }
        $manager->flush();
    }
}
