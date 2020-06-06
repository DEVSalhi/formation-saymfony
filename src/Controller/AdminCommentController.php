<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\AdminCommentType;
use App\Repository\CommentRepository;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminCommentController extends AbstractController
{
    /**
     * @Route("/admin/comments/{page}", name="admin_comments_index",requirements={"page"="\d+"})
     * @param CommentRepository $repo
     * @param int $page
     * @param PaginationService $pagination
     * @return Response
     */
    public function index(CommentRepository $repo,$page=1,PaginationService $pagination)
    {
        $pagination->setEntityClass(Comment::class)
            ->setPage($page);
        return $this->render('/admin/comments/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("admin/comments/{id}/edit",name="admin_comment_edit")
     * @param Comment $comment
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function edit(Comment $comment,Request $request,EntityManagerInterface $manager){
    $form=$this->createForm(AdminCommentType::class,$comment);
    $form->handleRequest($request);
    if($form->isSubmitted() && $form->isValid()){
        $manager->persist($comment);
        $manager->flush();
        $this->addFlash('success',"Le commentaire est bien changé  {$comment->getId()}");
    }
   return $this->render('admin/comments/edit.html.twig',['form'=>$form->createView(),'comment'=>$comment]);

    }

    /**
     * @Route("admin/comment/{id}/delete",name="admin_comment_delete")
     * @param Comment $comment
     * @param EntityManagerInterface $manager
     * @return RedirectResponse
     */
    public function delete(Comment $comment,EntityManagerInterface $manager){
        $manager->remove($comment);
        $manager->flush();
        $this->addFlash("success","le commentaire en question est bien supprimé ");
        return $this->redirectToRoute("admin_comments_index");

    }



}
