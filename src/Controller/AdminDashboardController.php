<?php

namespace App\Controller;

use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminDashboardController extends AbstractController
{
    // ____________________________________________________Decouverte des requetes DQL__D16-V2_

    /**
     * @Route("/admin", name="admin_dashboard")
     * l'object manager ne passait pas j'ai donc choisis de le remplacer par 'EntityManagerInterface'
     */
    public function index(EntityManagerInterface $manager): Response
    {
        $users=$manager->createQuery('SELECT COUNT(u) FROM App\Entity\User u')->getSingleScalarResult();
        $ads=$manager->createQuery('SELECT COUNT(a) FROM App\Entity\Ad a')->getSingleScalarResult();
        $bookings=$manager->createQuery('SELECT COUNT(b) FROM App\Entity\Booking b')->getSingleScalarResult();
        $comments=$manager->createQuery('SELECT COUNT(c) FROM App\Entity\Comment c')->getSingleScalarResult();
        
        return $this->render('admin/dashboard/index.html.twig', [
            // 'stats' => [
            //     'users'=>$users,
            //     'ads'=>$ads,
            //     'bookings'=>$bookings,
            //     'comments'=>$comments
            // ] , on peux utiliser la fonction compact qui va renvoyer un taleau dont pour la clé 'users' la donnée '$users' et ainsi de suite
            
            'stats' =>compact('users','ads','bookings','comments')
                
        ]);
    }
}
