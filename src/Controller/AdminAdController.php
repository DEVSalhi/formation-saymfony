<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Form\AdType;
use App\Repository\AdRepository;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminAdController extends AbstractController
{
    /**
     * @Route("/admin/ads/{page}", name="admin_ads_index",requirements={"page"="\d+"})
     * @param AdRepository $repo
     * @param int $page
     * @param PaginationService $pagination
     * @return Response
     */
    public function index(AdRepository $repo,$page =1,PaginationService $pagination)
    {
        $pagination->setEntityClass(Ad::class)
                    ->setPage($page);

        return $this->render('admin/ad/index.html.twig', [
            'pagination' => $pagination,

        ]);
    }

    /**
     * Permet d'afficher le formulaire d'edition
     * @Route("/admin/ads/{id}/edit",name="admin_ads_edit")
     * @param Ad $ad
     * @param Request $request
     * @return Response
     */

    public function  edit(Ad $ad,Request $request,EntityManagerInterface $manager){
        $form= $this->createForm(AdType::class,$ad);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($ad);
            $manager->flush();

            $this->addFlash('success',"l'annonce <strong>{$ad->getTitle()}</strong>  a bien été enregistrer");

        }
        return $this->render('admin/ad/edit.html.twig',[
            'ad'=>$ad,
            'form'=>$form->createView()
        ]);
    }
}
