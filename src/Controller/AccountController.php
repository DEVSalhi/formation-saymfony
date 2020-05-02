<?php

namespace App\Controller;

use App\Entity\PasswordUpdate;
use App\Entity\ResetPassword;
use App\Entity\User;
use App\Form\AccountType;
use App\Form\EmailResetType;
use App\Form\PasswordUpdateType;
use App\Form\RegistrationType;
use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AccountController extends AbstractController
{
    /**
     * @Route("/login", name="login_account")
     */
    public function login(AuthenticationUtils $utils)
    {
        $error=$utils->getLastAuthenticationError();
        $userame=$utils->getLastUsername();
        //dump($error);
        return $this->render('account/login.html.twig',['hasError'=>$error !== null,'username'=>$userame]);
    }

    /**
     * @Route("/logout",name="logout_account")
     */
    public function logout(){

    }

    /**
     * permet d'afficher le formulaire d'inscription
     *
     * @Route("/register",name="account_register")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */

    public function register(Request $request,EntityManagerInterface $manager,UserPasswordEncoderInterface $encoder ){
        $user=new User();

        $form=$this->createForm(RegistrationType::class,$user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $hash=$encoder->encodePassword($user,$user->getHash());
            $user->setHash($hash);
            $manager->persist($user);
            $manager->flush();
            $this->addFlash('success','votre compte a été bien créé ! vous pouvez maintenant connecter');

            return  $this->redirectToRoute('login_account');
        }

       return $this->render('account/registration.html.twig',['form'=>$form->createView()]);


    }

    /**
     * @Route("/account/profile",name="account_profile")
     * @return  Response
     */
    public function profile(Request $request,EntityManagerInterface $manager){
        $user=$this->getUser();
        $form=$this->createForm(AccountType::class,$user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($user);
            $manager->flush();
            $this->addFlash('success',"les données ont été enregistré avec succès");
        }
        return $this->render('account/profile.html.twig',['form'=>$form->createView()]);
    }

    /**
     * Permet de modifier le mot de passe
     * @Route("/account/update-password",name="account_password")
     * @return Response
     */
    public function updatePassword(Request $request,EntityManagerInterface $manager,UserPasswordEncoderInterface $encoder){

        $passwordUpdate=new PasswordUpdate();
        $form=$this->createForm(PasswordUpdateType::class,$passwordUpdate);
        $form->handleRequest($request);
        $user=$this->getUser();

        if($form->isSubmitted() && $form->isValid()) {
            if(!password_verify($passwordUpdate->getOldPassword(),$user->getHash()))
            {
                $form->get('oldPassword')->addError(new FormError("votre ancien mot de passe est incorrecte"));


            }else{

                $newPassword=$passwordUpdate->getNewPassword();
                $hash=$encoder->encodePassword($user,$newPassword);
                $user->setHash($hash);
                $manager->persist($user);
                $manager->flush();
                $this->addFlash('success','votre mot de passe a bien été modifié');
                return  $this->redirectToRoute('homepage');
            }
        }
        return $this->render('account/password.html.twig', ['form' => $form->createView()]);

    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserRepository $repo
     * @param \Swift_Mailer $mailer
     * @return Response
     *
     * @Route("/account/reset-password",name="account_reset")
     */
    public function resetPassword(Request $request,EntityManagerInterface $manager,UserRepository $repo,\Swift_Mailer $mailer ){

        $form=$this->createForm(EmailResetType::class);
        $form->handleRequest($request);
        if( $form->isSubmitted() && $form->isValid()){
            $user=$repo->findOneByEmail($form->getData()['email']);
            if(!empty($user)){

                $token=uniqid();
                $user->setResetPassword($token);
                $manager->persist($user);
                $manager->flush();
                $message=(new \Swift_Message('Hello Email'))
                    ->setFrom('salhi.sofien@gmail.com')
                    ->setTo('salhi.sofien@gmail.com')
                    ->setBody(
                        $this->renderView(
                            'emails/reset-password.html.twig', array('token' => $token)
                        ),
                        'text/html'
                    );

                $mailer->send($message);
                $this->addFlash('success','Consulter votre boite email');

            }else{
                $this->addFlash('danger','verifier votre email');

            }

        }

        return $this->render('account/reset-password.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @param $resetPassword
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordEncoderInterface $encoder
     * @param UserRepository $repo
     * @return Response
     *
     * @Route("/reset/{resetPassword}",name="reset_account")
     */
    public function resetPasswordToken($resetPassword,Request $request,EntityManagerInterface $manager,UserPasswordEncoderInterface $encoder,UserRepository $repo){

        $user=$repo->findOneByResetPassword($resetPassword);

      if(!empty($user)){
          $resetPasswordEntity=new ResetPassword();
          $form=$this->createForm(ResetPasswordType::class,$resetPasswordEntity);
          $form->handleRequest($request);
          if($form->isSubmitted() && $form->isValid()){
              $newPassword=$resetPassword->getNewPassword();
              $hash=$encoder->encodePassword($user,$newPassword);
              $user->setHash($hash);
              $user->setResetPassword(null);
              $manager->persist($user);
              $manager->flush();
              $this->addFlash('success','Votre mot de passe est bien initialisé');
              return $this->redirectToRoute('login_account');
          }
          return $this->render('account/reset-password-token.html.twig',['form'=>$form->createView()]);
      }else{
          $this->addFlash('danger','votre lien n\'est plus valable veuillez reinitialiser votre mot de passe de nouveau' );
        return $this->redirectToRoute('login_account');
      }

    }


    /**
     * Permet d'afficher le profil de l'utlisateur
     * @Route("/account",name="account_index")
     * @return Response
     */
    public function myAccount(){
        if(!empty($this->getUser()))
        return $this->render('user/index.html.twig',[
            'user'=>$this->getUser()
        ]);
        return $this->redirectToRoute('login_account');

    }
}
