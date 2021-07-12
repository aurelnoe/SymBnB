<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Service\UserService;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use App\Security\AppAuthenticator;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register", methods={"GET","POST"})
     */
    public function register(Request $request, GuardAuthenticatorHandler $guardHandler, AppAuthenticator $authenticator,UserService $service): Response
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {          
            $service->addUser($user,$form);

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Permet d'afficher et de traiter le formulaire de modification du profil
     * 
     * @Route("/register/profile", name="app_profile")
     * 
     * @IsGranted("ROLE_USER")
     *
     * @return Response
     */
    public function profile(Request $request) {

        $user = $this->getUser();

        $form = $this->createForm(AccountType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {   
            $this->service->editUser($user);

            $this->addFlash(
                'success',
                "Les données du profile ont été enregistrées avec succès !"
            );
        } 

        return $this->render('registration/profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Permet de modifier le mot de passe
     * 
     * @Route("/register/password-update", name="app_password")
     * 
     * @IsGranted("ROLE_USER")
     *
     * @return Response
     */
    public function updatePassword(Request $request, UserPasswordEncoderInterface $encoder) {

        $passwordUpdate = new PasswordUpdate();

        $user = $this->getUser();

        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {   
            //1. Vérifer que le oldPassword soit le meme que le password de l'utilisateur
            if (!password_verify($passwordUpdate->getOldPassword(), $user->getPassword())) {
                //Gérer l'erreur
                $form->get('oldPassword')->addError(new FormError("Le mot de passe que vous avez tapé n'est pas votre mot de passe actuel !"));
            } else {
                $newPassword = $passwordUpdate->getNewPassword();
                $hash = $encoder->encodePassword($user,$newPassword);

                $user->setPassword($hash);

                $this->service->updatePassword($user);
                
                $this->addFlash(
                    'success',
                    "Le mot de passe a été modifié avec succès !"
                );

                return $this->redirectToRoute('home');
            }
        }
        return $this->render('registration/password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Permet d'afficher le profil de l'utilisateur connecté
     *
     * @Route("/account", name="account_index")
     * 
     * @IsGranted("ROLE_USER")
     * 
     * @return void
     */
    public function myAccount() {
        return $this->render('user/index.html.twig', [
            'user' => $this->getUser()
        ]);
    }
}
