<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Image;
use App\Form\AdType;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class AdController extends AbstractController
{
    /**
     * @Route("/ads", name="ads_index")
     */
    public function index(AdRepository $repo)
    {
        $ads=$repo->findAll();
        return $this->render('ad/index.html.twig', [
            'ads' => $ads
        ]);
    }



    /**
     * Peremt de creer une annonce
     * @Route("/ads/new",name="ads_create")
     * @IsGranted("ROLE_USER")
     */

    public function create(Request $request,EntityManagerInterface $manager){

        $user=$this->getUser();
        $ad=new Ad();
        $form=$this->createForm(AdType::class,$ad);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            foreach($ad->getImages() as $image){
                $image->setAd($ad);
                $manager->persist($image);

            }

            $ad->setAuthor($user);
            $manager->persist($ad);
            $manager->flush();
            $this->addFlash('success',"l'annonce <strong>{$ad->getTitle()}</strong> a bien été crée");


            return $this->redirectToRoute('ad_show',['slug'=>$ad->getSlug()]);
        }


        return $this->render('ad/new.html.twig',['form'=>$form->createView()]);
    }
    /**
     * @Route("/ads/{slug}",name="ad_show")
     * @return Response
     *
     */
    public function show(Ad $ad){

        return $this->render('ad/show.html.twig',['ad'=>$ad]);


    }

    /**
     * permet d'afficher le formulaire d'edition
     * @Route("/ads/{slug}/edit",name="ads_edit")
     * @Security("is_granted('ROLE_USER') and user === ad.getAuthor()",message="cette annonce ne vous appartient  pas, vous ne pouvez pas la modifier" )
     * @return Response
     */
    public function edit(Ad $ad,Request $request,EntityManagerInterface $manager){

        $form=$this->createForm(AdType::class,$ad);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            foreach($ad->getImages() as $image){
                $image->setAd($ad);
                $manager->persist($image);

            }
            $manager->persist($ad);
            $manager->flush();
            $this->addFlash('success',"l'annonce <strong>{$ad->getTitle()}</strong> a bien été modifé");


            return $this->redirectToRoute('ad_show',['slug'=>$ad->getSlug()]);
        }

        return $this->render('ad/edit.html.twig',['form'=>$form->createView()]);

    }

    /**
     * @param Ad $ad
     * @param EntityManagerInterface $manager
     *
     * @Route("/ads/{slug}/delete",name="ads_delete")
     * @Security("is_granted('ROLE_USER') and user === ad.getAuthor()",message="vous n'avez pas le droit d'accéder à cette annonce")
     * @return Response
     */
    public function  delete(Ad $ad,EntityManagerInterface $manager){

        $manager->remove($ad);
        $manager->flush();
        $this->addFlash('success',"l'annonce {$ad->getTitle()} est bien supprimé");
        return $this->redirectToRoute("ads_index");
    }
}
