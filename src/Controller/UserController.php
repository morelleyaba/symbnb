<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class UserController extends AbstractController
{

    // ____________ Afficher le Profil d'un''utilisateur/un auteur d'annonce________________
    
   /**
    * Premiere methode avec le param converter / Mettre en paramettre l'entity concerné
    *
    *@Route("/user/{slug}", name="user_show")
    * @param User $user
    * @return Response
    */
    public function index(User $user): Response
    {

        return $this->render('user/index.html.twig', [
            'user' => $user,
        ]);
    }


// /**
//  * Deuxieme method - Repository / Afichage de profil ------------------------------------
//  * 
//  *@Entity("User", expr="UserRepository.find(User)")
//  * @Route("/user/{slug}", name="user_show")
//  * @return Response
//  */
//     public function profil($slug,UserRepository $userRepository): Response
//     {

//         $user=$userRepository->findOneBySlug($slug);
//         return $this->render('user/index.html.twig', [
//             'user' => $user,
//         ]);
//     }


    //  ________________Afficher mon compte(moi qui suis connecté) /profil utilisateur connecté________________________

    /**
     * Permet d'afficher le profil de l'utilisateur connecté
     *
     * @Route("/account", name="account_index")
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function myAccount(){

        // la variable user sera egale a l'utilisateur connecté qu'on va recuperer grace a ("getUser")
        return $this->render("user/index.html.twig",[
            "user"=>$this->getUser()
        ]);
    }
}

 