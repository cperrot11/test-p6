<?php

namespace App\Controller;

use App\Form\RegistrationType;
use App\Repository\UserRepository;
use App\Security\LoginFormAuthentificatorAuthenticator;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;

class SecurityController extends AbstractController
{
    /**
     * @Route("/inscription", name="security_registration")
     * @Route("manage/user/{id}", name="user_update")
     */
    public function registration(User $user=null, Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder, GuardAuthenticatorHandler $authenticatorHandler, LoginFormAuthentificatorAuthenticator $authenticator) {
        if(!$user){
            $user = new User();
        }

        /* reli les champs du formulaire à l'User */
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $manager->persist($user);
            $manager->flush();

//            return $this->redirectToRoute("app_login", [
//                'user'=> $user
//            ]);
            return $authenticatorHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'home'
            );

        }

        return $this->render('security/registration.html.twig', [
            'title'=> "Enregistrement",
            'form'=> $form->createView()
        ]);
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }


    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout() {
    }
}
