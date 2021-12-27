<?php

namespace App\Controller;

use DateTime;
use App\Entity\Ad;
use Faker\Factory;
use App\Entity\Booking;
use App\Entity\Comment;
use App\Form\BookingType;
use App\Form\CommentType;
use Cocur\Slugify\Slugify;
use App\Repository\AdRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// _________________Reservation d'un appartement____Premiere methode______________

class BookingController extends AbstractController
{
    /**
     * Premiere methode, j'ai du ajouter le adrepository a la L-6 de l'entity ad afin que mon param converter passe
     * @Route("/ads/{slug}/book", name="booking_create")
     * autorisé aux personnes connectées
     *  @IsGranted("ROLE_USER")
     * @param Ad $ad
     * @return Response
     */
    public function book(Ad $ad,Request $request): Response
    {
            // creer un nouvel objet , une reservation 
            $booking= new Booking;

            $em=$this->getDoctrine()->getManager();
            $formBook=$this->createForm(BookingType::class,$booking);
            $formBook->handleRequest($request);
                
            if ($formBook->isSubmitted() && $formBook->isValid()) {
                    // recuperer l'utilisateur connecté,celui qui fait la reservation
                    $user=$this->getUser();
                    // _____________________________affecter les differentes valeurs a l'objet de la reservation
                    
                    #Gestion du slug de la reservation (ajouter par moi meme afin de ne pas utiliser l'id de la reservaton en paramettre de l'url pour plus de securité)
                    $faker=Factory::create('fr-Fr');
                    $fakeId=$faker->creditCardNumber;
                    #----------------
                    
                    $booking->setBooker($user) 
                            ->setAd($ad)
                            // ->setStartDate($booking->getStartDate())
                            // ->setEndDate($booking->getEndDate())
                            // ->setCreatedAt() allez voir dans l'entity "booking.php" vu que la date de creation doit s'incrementer automatiquement
                            // ->setAmount("")pareil pour le prix du sejour
                           //   getComment() pour recuperer le commentaire actuel venant du formulaire
                            ->setComment($booking->getComment())
                            # Affecter le slug du booking
                            ->setSlug($fakeId);
                // ______________________________________Avant de persister l'annonce____

                        #si les date ne sont pas disponibles, message d'erreur a l'utilisateur
                        if (!$booking->isBookabbleDates()) {
                                $this->addFlash(
                                        "warning",
                                        "les dates que vous avez choisis ont deja été reservé pour cette annonce"
                                ); 
                        }#sinon
                        else {
                                
                                $em->persist($booking);
                                $em->flush();
                                // passer "le slug" de la reservation par l'url de la page de redirection "booking_show"
                                // passer "le slug" de la reservation par l'url de la page de redirection "booking_show"
                                        return $this->redirectToRoute("booking_show",[
                                                "slug"=>$booking->getSlug(),
                                                "withAlert"=>true
                                        ]);
                        
                        }
                // ______________________________________
            }
            return $this->render('booking/book.html.twig', [
                    'ad'=>$ad,
                    'formBook' => $formBook->createView(),
            ]);
    }

        // __________________Afficher la page de success apres la reservation______avec l'interieure le formulaire de commentaires de satisfation sur l'annonce_______

        /**
         * On ne commente pas la reservation, c'est l'annonce qu'on commente
         *@Route("/booking/{slug}", name="booking_show")
         * @param Booking $booking
         * @param Request $request
         * @return Response
         */
        public function show(Booking $booking,Request $request){ 

                $comment=new Comment();
                // creeons notre formulaire
                $formComment=$this->createForm(CommentType::class,$comment);
                $formComment->handleRequest($request);
                if ($formComment->isSubmitted() && $formComment->isValid()) {
                        // recuperons l'utilisateur connecté qui a effectué l'annonce
                        $author=$this->getUser();
                        // recuperons l'annonce qui a été reservé qui de booking(en paramettre)et a partir de booking recuperer le "ad" de la proprieté "ad"[ad_id] de l'entity "booking"
                        $ad=$booking->getAd();
                        $comment->setAd($ad)
                                ->setAuthor($author);

                        $em=$this->getDoctrine()->getManager();
                        // demander a doctrine de persister le commentaire
                        $em->persist($comment);
                        $em->flush();

                        // message flash
                        $this->addFlash(
                                'success',
                                "Votre commentaire a bien été enregistrer"
                        );
                }

                return $this->render("booking/show.html.twig",[
                        'booking'=>$booking,
                        'formComment'=>$formComment->createView(),
                ]);
        }
        

    // ______________2me methode__________Reserver un appartement______(pas pris le jaascript en compte)____

    // /**
    //  * avant l'utilisation de cette methode, veuillez suprimer les configuration ajouter mannuellement dans la table "booking"(le cycle de vie) vu que tout se fera ici directement
    //  * @Route("/ads/{slug}/book", name="booking_create")
    //  * @return Response
    //  */
    // public function reservation(AdRepository $adRepository,$slug,Request $request): Response
    // {
    //     // l'annonce dont le slug = slug passé en paramettre
    //     $article_book = $adRepository->findOneBy(['slug'=>$slug]);
    //     $booking= new Booking;

    //     $formBook=$this->createForm(BookingType::class,$booking);
    //     $formBook->handleRequest($request);
    //     $em=$this->getDoctrine()->getManager();
    //     if ($formBook->isSubmitted() && $formBook->isValid()) {
    //             // recuperer l'utilisateur connecté,celui qui fait la reservation
    //             $user=$this->getUser();
    //             //  determiner le nbre de jours = (date de fin - date de debut) grace a la fonction "diff"
    //             $dureSejour = $booking->getEndDate()->diff($booking->getStartDate())->days;
    //             // determiner le prix du sejour (prix de l'annonce * nbre de jour)
    //             $amount = $article_book->getPrice()*$dureSejour;
    //             // determiner la date de creation
    //             $creatdate= new DateTime();

    //              // _____________________________affecter les differentes valeurs a l'objet de la reservation
    //                 $booking->setBooker($user)
    //                         ->setAd($article_book)
    //                         ->setStartDate($booking->getStartDate())
    //                         ->setEndDate($booking->getEndDate())
    //                         ->setCreatedAt($creatdate)
    //                         ->setAmount($amount)
    //                         ->setComment($booking->getComment());

    //                 $em->persist($booking);
    //                 $em->flush();
    //                 return $this->redirectToRoute("homepage",["id"=>$booking->getId()]);


    //     }
    //     return $this->render('booking/book.html.twig', [
    //         'ad'=>$article_book,
    //         'formBook' => $formBook->createView(),
    //     ]);
    // }
}
