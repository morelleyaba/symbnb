<?php

namespace App\Controller; 

use App\Entity\Ad;
use App\Form\AdType;
use App\Repository\AdRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class AdminAdController extends AbstractController 
{
    // _______________________________________Afficher toutes les annonces chez l'admin__D14-V5__
    /**
     * Afficher toutes les annonces , 
     * l'espace admin doit etre accessible uniquement a l'administrateur (voir le fichier security.yaml / Avant derniere ligne)
     * 
     * systeme de pagination, l'url doit recevoir en parametre la variable 'page'
     *  @Route("/admin/ads/{page<\d+>?1}", name="admin_ads_index")
     * 
     * faire en sorte que lorsque l'admin n'arrive pas a acceder aux pages qui  lui sont destiné,
     *  il soit redirigé vers sa page de connexion personnalisé et non la page de connexion des utilisateurs qui a été improvisé lorsque je creais le form-auth, voir(le fichier security.yaml)[les firewall]
     * @param AdRepository $ad
     * @return Response 
     */
    public function index(AdRepository $adRepo, $page): Response
    {
        // ________________________________systeme de pagination__D15-V2
        $limit=3;
        $start=$page * $limit - $limit;
        // 1 * 10 = 10 - 10 = 0
        // 2 * 10 = 20 - 10 = 10

        // Rendre dynamique la pagination, determiner le nbre de page qu'on peut afficher en fonction du nbre d'annonces divisé par la limit
        $total= count($adRepo->findAll());
        $pages = ceil($total/$limit); # 3.4 => 4 [arrondie a 4 par la fonction "ceil()"]



        return $this->render('admin/ad/index.html.twig', [
            // 'ads' => $adRepo->findAll(), / a cause de la pagination utilisons plutot findBy()
            'ads' => $adRepo->findBy([],[],$limit,$start),
            'pages'=>$pages,
            'page'=>$page,
        ]);
    }

    // _____________edition d'un article par l'admin_______________
    
    /**
     * Afficher le formulaire d'edition / utiliser le formType "AdType" de l'annonce
     * @Route("/admin/ads/{slug}/edit", name ="admin_ads_edit")
     * @param Ad $ad
     * @return void
     */
    public function edit (Ad $ad,Request $request)
    {
        $form=$this->createForm(AdType::class,$ad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em=$this->getDoctrine()->getManager();
            // faire persister l'$ad
            $em->persist($ad);
            $em->flush();

            // notification
            $this->addFlash(
                "success",
                "L'annonce <strong>{$ad->getTitle()}</strong> ! a bien été enregistré"
            );

        }
        return $this->render('admin/ad/edit.html.twig',[
             'formEdit'=>$form->createView(),
             'ad'=>$ad
        ]);
    }

    // ____________supprimer une annonce par l'admin

    /**
     * suprimier une annonce par l'admin
     * @ROUTE("/admin/ads/{slug}/delete", name ="admin_ads_delete")
     * @param Ad $adrepo
     * @return void
     */
    public function delete(Ad $ad)
    {
        // si l'annonce a supprimer possede deja des reservations, empecher la suppression en vu de garder les historiques des reservation 
        if (count($ad->getBookings())>0) {
            $this->addFlash(
                "warning",
                "Vous ne pouvez pas supprimer l'annonce <strong>{$ad->getTitle()}</strong>, elle possede deja des reservations"
            );
        }else {
            $em=$this->getDoctrine()->getManager();
            $em->remove($ad);
            $em->flush();
            // notification
            $this->addFlash(
                "success",
                "L'annonce <strong>{$ad->getTitle()}</strong> ! a bien été supprimé"
            );
        }
        return $this->redirectToRoute('admin_ads_index');
    }
}

