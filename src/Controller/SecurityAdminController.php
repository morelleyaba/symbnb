<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityAdminController extends AbstractController
{
    /**
     * @Route("/admin/login", name="admin_account_login")
     * video 7 D-14 pas encore terminé 5eme minutes / Gestion des erreure
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path'); 
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('admin/security/Adminlogin.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * permet aux administrateurs de se deconnecter
     * la route doit debuter par 'admin'
     * pour la petite histoire a chaque fois que l'administrateur se deconnectait, on etait dirigé vers le formulaire des utilisateurs, ce qui n'est pas normale, il a fallu donc creer une nouvelle route de deconnexion pour l'admin
     * @Route("/admin/logout", name="admin_account_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
