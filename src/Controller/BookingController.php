<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Entity\Comment;
use App\Form\BookingFormType;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class BookingController extends AbstractController
{
    /**
     * @Route("/ads/{slug}/book", name="booking_create")
     * @IsGranted("ROLE_USER")
     */
    public function book(Ad $ad,Request $request,EntityManagerInterface $manager )
    {
        $booking = new Booking();
        //validation groups on peut la mettre ici ou dans le formulaire bookingFormType
        //$form=$this->createForm(BookingFormType::class,$booking,['validation_groups'=>["front","default"]]);
        $form=$this->createForm(BookingFormType::class,$booking);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $user=$this->getUser();
            $booking->setBooker($user)
                ->setAd($ad);
            if(!$booking->isBookableDates()){
                $this->addFlash('warning','les dates sont déja prises');

            }else{
            $manager->persist($booking);
            $manager->flush();

            return $this->redirectToRoute('booking_show',['id'=>$booking->getId(),'withAlert'=>true]);
            }
        }

        return $this->render('booking/book.html.twig', [
            'ad' => $ad,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Booking $booking
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     * @Route("/booking/{id}",name="booking_show")
     * @IsGranted("ROLE_USER")
     */

    public function show(Booking $booking,Request $request,EntityManagerInterface $manager){

        $comment=new Comment();
        $form=$this->createForm(CommentType::class,$comment);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $comment->setAd($booking->getAd())
                ->setAuthor($this->getUser());

            $manager->persist($comment);
            $manager->flush();

            $this->addFlash('success',"Votre commentaire a été bien pris en compte");

        }


        return $this->render('booking/show.html.twig',['booking'=>$booking,'form'=>$form->createView()]);
    }
}
