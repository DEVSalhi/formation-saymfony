<?php


namespace App\Controller;

use App\Repository\AdRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class HomeController extends  AbstractController {


    /**
     * @Route("/",name="homepage")
     * @param AdRepository $adRepo
     * @return Response
     */
    public function home(AdRepository $adRepo,UserRepository $userRepo)
    {

        return $this->render('home.html.twig',[
            'ads'=>$adRepo->findBestAds(3),
            'users'=>$userRepo->findBestUser(3)
        ]);
    }

    /**
     * @Route("/hello/{prenom}/age/{an}", name="hello",requirements={"an"="\d+"})
     * @Route("/hello",name ="hello_base")
     * @Route("hello/{prenom}",name="hello_prenom")
     * @param string $prenom
     * @param int $an
     * @return Response
     */
    public function hello($prenom="anomnuyme",$an =0){



        return $this->render('hello.html.twig',['prenom'=>$prenom,'an'=>$an]);
    }




}

?>