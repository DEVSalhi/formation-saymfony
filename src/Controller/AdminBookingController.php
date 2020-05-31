<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\AdminBookingType;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminBookingController extends AbstractController
{
    /**
     * @Route("/admin/bookings", name="admin_booking_index")
     * @param BookingRepository $repo
     * @return Response
     */
    public function index(BookingRepository $repo)
    {
        $bookings=$repo->findAll();
        return $this->render('admin/booking/index.html.twig', [
            'bookings' => $bookings,
        ]);
    }

    /**
     * @Route("admin/bookings/{id}/delete",name="admin_booking_delete")
     * @param Booking $booking
     * @param EntityManagerInterface $manager
     * @return RedirectResponse
     */
    public function delete(Booking $booking,EntityManagerInterface $manager){
        $manager->remove($booking);
        $manager->flush();
        $this->addFlash("success","la reservation en question est bien supprimé ");
        return $this->redirectToRoute("admin_bookings_index");
    }

    /**
     * @Route("admin/booking/{id}/edit",name="admin_booking_edit")
     * @param Booking $booking
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function edit(Booking $booking,Request $request,EntityManagerInterface $manager){
    $form=$this->createForm(AdminBookingType::class,$booking);
    $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            //utiliser un evenement docrtine preupdate
            $booking->setAmount(0);
            $manager->persist($booking);
            $manager->flush();
            $this->addFlash('success',"la reservation n {$booking->getId()} a bien été modifiée");
        }
    return $this->render('admin/booking/edit.html.twig',['form'=>$form->createView(),'booking'=>$booking]);
    }
}
