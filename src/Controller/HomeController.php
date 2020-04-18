<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class HomeController extends  AbstractController {



    /**
     *@Route("/",name="homepage")
     */
    public function home()
    {
        $prenoms=['Lior'=>31,'Josef'=>22,'Soufien'=>30];
        $title='Bonjour à tous';
        return $this->render('home.html.twig',['title'=>$title,'age'=>31,'prenoms'=>$prenoms]);
    }

    /**
     * @Route("/hello/{prenom}/age/{an}", name="hello",requirements={"an"="\d+"})
     * @Route("/hello",name ="hello_base")
     * @Route("hello/{prenom}",name="hello_prenom")
     */
    public function hello($prenom="anomnuyme",$an =0){



        return $this->render('hello.html.twig',['prenom'=>$prenom,'an'=>$an]);
    }



}

?>