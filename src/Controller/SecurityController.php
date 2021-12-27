<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    // ------------------systeme de connexion / deconnexion -----------bin/console make:auth------------
    
    //Afficher et gerer le formulaire de connexion
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        //redirection apres connexion validée ou aller voir le fichier (" Controller\LoginAuthenticator.php ")
        // if ($this->getUser()) { 
        //     return $this->redirectToRoute('ads_index');
        // }

        // get the login error if there is one
        // l'erreur si les identifiants sont pas correctes,envoyez la variable 'error' qui contient '$error' a la vue 'security/login.html.twig'
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user / Recuppere le dernier nom d'utilasateur tapé 'last_username' envoyer egalement a la vue, pour permettre que le champ soit pre-remplir par ( l'identifiant qui vient d'etre saisie ) quand la premiere tentative de connexion echoue
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
             'error' => $error]);
    }

    /**
     * permet aux utilisateurs de se deconnecter
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
