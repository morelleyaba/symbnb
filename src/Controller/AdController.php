<?php

namespace App\Controller;
use App\Entity\Ad;
use App\Form\AdType;
use App\Entity\Image;
use App\Repository\AdRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdController extends AbstractController
{
    /**
     * Afficher les annonces venant de la table---------------------------------
     * @Route("/ads", name="ads_index")
     * injection de dependance
     */
    public function index(AdRepository $repo): Response
    {
            $ads = $repo    
                ->findAll();
        return $this->render('ad/index.html.twig', [
            'ads' => $ads
        ]);
    }



    //----------------------------------permet de creer une annonce--------------
    /**
     * permet de creer une annonce
     * @Route("/ads/new", name="ads_create")
     * @return Response
     */
    public function create(Request $request){

        $ad = new Ad();

         //Relier le formulaire issu de ["AdType"] a la class de l'entity (Ad)
        $form=$this->createForm(AdType::class, $ad);
        
        // analyser la requete du formulaire pour relier toutes les informations qui se retrouvent dans le formulaire dans notre variable annonce $ad
        $form->handleRequest($request); 

        // verifier si le formulaire a été submit et est valide
        if ($form->isSubmitted() && $form->isValid()){
        
             $entityManager = $this->getDoctrine()->getManager(); 
            //  [ObjectManager $entityManager en paramettre Ligne 40]
            // Gerons les images de collections
            foreach ($ad->getImages() as $image) {
                $image->setAd($ad);
                $entityManager->persist($image);
            }
            

            $ad->setAuthor($this->getUser());

            $entityManager->persist($ad);
            $entityManager->flush();
            // redirection vers une autre page
            // a cause de la fonction 
            
                        // notification
                     $this->addFlash(
                        "success",
                        "L'annonce <strong>{$ad->getTitle()}</strong> !a bien été enregistrer"
                    );
                    
            return $this->redirectToRoute('ads_show',
            ['slug'=>$ad->getSlug()
        ]);
        }
        return $this->render('ad/new.html.twig',[
            'formulaire'=>$form->createView()
        ]);
        
       
    }

    //--------------------------------Afficher les infos d'un article accompagné de l'auteur------------------------
    /**
     * repository qui permet de selectionner les donnée au sein d'une table
     * permet d'afficher une seule annonce
     * on passe slug en paramettre (par l'url) voir fichier index.html.twig L-22
     * @Route("ads/{slug}", name="ads_show")
     * 
     * @Entity("Ad", expr="AdRepository.find(Ad)")
     * @return Response
     */
    public function show($slug, AdRepository $repo){

        // si on a un chanps 'title' dans notre fichier ad.php alors on peut ecrire 'findByTitle' aussi
        // il suffit d'un champs qui existe dans la table/entité

        // je recupere l'annonce qui corrspond au slug !
        // findOneBy 

          $ad=$repo->findOneBySlug($slug); //  -> mis en commentaire pour le paramconverter
        // L-31 suprimer ($slug, AdRepository $repo) dans le paramettre de la fonction show()
            return $this->render('ad/show.html.twig', [
                'ad' => $ad
            ]);

    }

    //-------------------------------- Edition d'une annnonce ------------------------
    // editer une annonce on peux utiliser le paraConverter comme utiliser le repository, dans notre cas on va utiliser le repository
    /**
     *  Afficher le formulaire d'edition
     * @Route("/ads/{slug}/edit", name="ads_edit")
     * @return Response
     */
    public function edit($slug, AdRepository $repo,Request $request):Response
    {
        // $ad=$adRepository->find($slug);
        $ad=$repo->findOneBySlug($slug); 
         //Relier le formulaire issu de ["AdType"] a la class de l'entity (Ad)
        $edit_form= $this->createForm(AdType::class,$ad);
        $edit_form ->handleRequest($request);

        // Validation du formulaire
                if($edit_form->isSubmitted() && $edit_form->isValid()){
                    $em=$this->getDoctrine()->getManager();
                     //validation du sous formulaire
                     foreach ($ad->getImages() as $image) {
                        $image->setAd($ad);
                        $em->persist($image);
                    }
                    $em->persist($ad);
                $em->flush();
                // notification
                $this->addFlash(
                    "success",
                    "L'annonce <strong>{$ad->getTitle()}</strong> ! a bien été modifié"
                );
                return $this->redirectToroute('ads_show',
                ['slug'=>$ad->getSlug()
             ]);
        }

        return $this->render('ad/edit.html.twig',[
            'form_edit'=>$edit_form->createView(),
            //Envoyer le $ad qui contient les données de l'entity de l'annonce concerné
            'ad'=>$ad
        ]);
    }


}
