<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Verification;
use App\Form\ConfirmationMailType;
use App\Form\SetPasswordType;
use App\Repository\UserRepository;
use App\Repository\VerificationRepository;
use Faker\Factory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Constraints\DateTime;

#[Route('/')]
class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = "";
        //S'il y a une erreur lors de la connexion
        if($authenticationUtils->getLastAuthenticationError() != ""){
            $error = "Login et/ou mot de passe invalide(s) !";
        }
        //On récupère le dernier login entré
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout()
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }

    #[Route('/redirection', name: 'app_login_redirection')]
    public function redirection(UserRepository $repository, AuthenticationUtils $utils): Response
    {
        $user = $repository->findOneBy([
            'login' => $utils->getLastUsername(),
        ]);
        if($user->getRoles()[0] == "ROLE_ELEVE"){
            if($user->getPasswordConfirm() == "non"){
                return $this->redirectToRoute('app_set_password');
            }
            return $this->redirectToRoute('app_eleve_dashboard');
        }
        elseif($user->getRoles()[0] == "ROLE_MONITEUR"){
            if($user->getPasswordConfirm() == "non"){
                return $this->redirectToRoute('app_set_password');
            }
            return $this->redirectToRoute('app_moniteur_dashboard');
        }
        elseif($user->getRoles()[0] == "ROLE_GERANTE"){
            return $this->redirectToRoute('app_user_dashboard');
        }
        return $this->redirectToRoute('app_login');
    }

    #[Route('setPassword', name: 'app_set_password', methods: ['GET', 'POST'])]
    function setPassword(Request $request, UserPasswordHasherInterface $hasher, UserRepository $userRepository){
        $user = new User();
        $confirmation = "";
        $error = "";

        $form = $this->createForm(SetPasswordType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            if($user->getPassword() === $user->getPasswordConfirm()){
                $this->getUser()->setPassword($hasher->hashPassword($user, $user->getPassword()));
                $this->getUser()->setPasswordConfirm('oui');
                $userRepository->save($this->getUser(), true);
                $confirmation = "Votre nouveau mot de passe est enregistré, vous pouvez vous connectez avec ce dernier.";
            }
            else{
                $error = "Les mots de passe sont différents";
            }
        }

        return $this->renderForm('email/password.html.twig',[
            'error' => $error,
            'form' => $form,
            'confirmation' => $confirmation,
        ]);
    }
}
