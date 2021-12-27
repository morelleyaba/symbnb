<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminAccountController extends AbstractController
{
    //________________Afficher le formulaire de onnection de l'admin / qui doit pas etre le meme que celui des autres utilisateurs
    /**
     * @Route("/admin/login", name="admin_account_login")
     * 
     * faire en sorte que lorsque l'admin n'arrive pas a acceder aux pages qui  lui sont destiné, il soit redirigé vers sa page de connexion personnalisé et non la page de connexion des utilisateurs qui a été improvisé lorsque je creais le form-auth, voir(le fichier security.yaml) [les firewall]
     * les "[les firewall]" ont leurs caracteristiques(on deja "dev" et "main"[le main s'adresse a tous les urls]),ce sont des parties de mon application que je decide de securiser ou de ne pas securiser / creeons un firewall "admin" en "dev" et "man"
     */
    public function index(): Response 
    {
        return $this->render('admin/account/login.html.twig', [
            'controller_name' => 'AdminAccountController',
        ]);
    }
}
