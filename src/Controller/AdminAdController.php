<?php

namespace App\Controller; 

use App\Repository\AdRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminAdController extends AbstractController
{
    // _______________________________________Afficher toutes les annonces chez l'admin__D14-V5__
    /**
     * Afficher toutes les annonces , 
     * l'espace admin doit etre accessible uniquement a l'administrateur (voir le fichier security.yaml / Avant derniere ligne)
     *  @Route("/admin/ads", name="admin_ads_index")
     * 
     * faire en sorte que lorsque l'admin n'arrive pas a acceder aux pages qui  lui sont destiné, il soit redirigé vers sa page de connexion personnalisé et non la page de connexion des utilisateurs qui a été improvisé lorsque je creais le form-auth, voir(le fichier security.yaml)[les firewall]
     * @param AdRepository $ad
     * @return Response 
     */
    public function index(AdRepository $adRepo): Response
    {
        return $this->render('admin/ad/index.html.twig', [
            'ads' => $adRepo->findAll(),
        ]);
    }
}
