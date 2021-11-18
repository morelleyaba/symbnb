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

        // un example par rapport au sous-formulairede type collection
        // $image3 = new Image();
        // $image3->setUrl('https://www.akamai.com/content/dam/site/im-demo/perceptual-standard.jpg?imbypass=true')
        //       ->setCaption('Titre 3');

        //       $image35 = new Image();
        // $image35->setUrl('https://www.akamai.com/content/dam/site/im-demo/perceptual-standard.jpg?imbypass=true')
        //       ->setCaption('Titre 35');


        //       $ad->addImage($image3)
        //          ->addImage($image35);


        $form=$this->createForm(AdType::class, $ad);
        
        // analyser la requete du formulaire pour relier toutes les informations qui se retrouvent dans le formulaire dans notre variable annonce $ad
        $form->handleRequest($request);

        // verifier si le formulaire a été submit et est valide
        if ($form->isSubmitted() && $form->isValid()){
           
             $entityManager = $this->getDoctrine()->getManager(); 
            //  [ObjectManager $entityManager en paramettre Ligne 38]
            foreach ($ad->getImages() as $image) {
                $image->setAd($ad);
                $entityManager->persist($image);
            }
            $entityManager->persist($ad);
            $entityManager->flush();
            // redirection vers une autre page
            // a cause de la fonction 
            
                        // notification
                     $this->addFlash(
                        "success",
                        "L'annonce <strong>{$ad->getTitle()}</strong> !a bien été enregistrer"
                    );
                    
            return $this->redirectToRoute('ads_show',['slug'=>$ad->getSlug()]);
        }
        return $this->render('ad/new.html.twig',[
            'formulaire'=>$form->createView()
        ]);
        
        // $form=$this->createFormBuilder($ad)
        //             ->add('title')
        //             ->add('introduction')
        //             ->add('content')
        //             ->add('rooms')
        //             ->add('price')
        //             ->add('coverImage')
        //             // ajouter des proprietés avec le systeme de class (attr ->attribut) a notre boutton
        //             ->add('save', SubmitType::class, 
        //             ['label' => 'Créer la nouvelle annonce',
        //             'attr'=>[
        //                 'class'=>'btn btn-primary'
        //             ]])
        //             ->getForm();
        
       
    }

    //--------------------------------Afficher un article------------------------
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
    // editer une annonce on peux utiliser le paraConverter comme utiliser le repository
    /**
     *  Afficher le formulaire d'edition
     * @Route("/ads/{slug}/edit", name="ads_edit")
     * @return Response
     */
    public function edit($slug,AdRepository $adRepository,Request $request):Response
    {
        $ad=$adRepository->find($slug);

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
                return $this->redirectToroute('ads_show');
        }

        return $this->render('ad/edit.html.twig',[
            'form_edit'=>$edit_form->createView(),
            //Envoyer le $ad qui contient les données de l'entity de l'annonce concerné
            'ad'=>$ad
        ]);
    }


}
