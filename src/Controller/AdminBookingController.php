<?php

namespace App\Controller; 

use App\Entity\Booking;
use App\Form\AdminBookingType;
use App\Repository\BookingRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminBookingController extends AbstractController
{
    // _________________________Affichage des commentaires________
    /**
     * @Route("/admin/bookings", name="admin_bookings_index")
     */
    public function index(BookingRepository $bookingRepo): Response
    {
        return $this->render('admin/booking/index.html.twig', [
            'bookings' => $bookingRepo->findAll(),
        ]);
    }

    // _____________edition d'un Commentaire par l'admin_________D14-V15 (explication des trois methodes d'edition)
    
    /**
     * editer un commentaire
     * @Route("/admin/bookings/{slug}/edit", name="admin_bookings_edit")
     * @param Booking $booking
     * @param Request $request
     * @return void
     */
    public function edit (Booking $booking,Request $request)
    {
        // 'AdminBookingType' le nouveau formType de la reservation 'Booking' de l'admin

            # Remarque lors du flush,on nous dis que la date d'arrivé doit etre ulterieure a la date d'aujourd'huit (contrainte ajouté dans "Booking.php L-41" pour les utilisateurs )
            # on fera alors intervenir les "groups de validation" ___________afin de lever l'exeption pour l'admin en ajoutant l'attribut ('validation_groups'=>["Default"]) tout comme on peut le laisser sans ajouter l'attribut.
            # Voir le bookingController.php afin de faire intervenir le [groups={"front"}] lors de la reservation du client ______________________D14-V22__
        
            $form=$this->createForm(AdminBookingType::class,$booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // recalculer le montant de ma reservation Apres edition de l'admin, qui est le prix de l'annonce 'price de l'entity Ad' * par la durée de reservation 'getDuration de l'entity Booking'
            // a partir de l'objet '$booking', pour avoir access au 'price' de l'annonce nous passons par la proprieté 'ad' qui les lie par relation de clée etrangere / La durée quand a elle provient de la fonction 'getDuration' defini dans (booking.php)
            $prix=$booking->getAd()->getPrice() * $booking->getDuration();
            $booking->setAmount($prix);
            // ou tous simplement mettre le '$booking->setAmount(0)'[pour mettre le montant a zero] et ajouter le '@ORM\PreUpdate' dans 'booking.php' L-77 , en mettant a jour quand la fonction 'persist' vera que le 'amount' = 0, il calculera le 'amount'

            $em=$this->getDoctrine()->getManager();
            // faire persister le $booking
            $em->persist($booking);
            $em->flush();

            // notification
            $this->addFlash(
                "success",
                "La reservation N <strong>{$booking->getId()}</strong> a bien été modifié"
            );

            return $this->redirectToRoute("admin_bookings_index");
        }
        return $this->render('admin/booking/edit.html.twig',[
             'formBook'=>$form->createView(),
             'booking'=>$booking
        ]);
    }

    // ____________supprimer un commentaire par l'admin

    /**
     * suprimer une annonce par l'admin
     * @ROUTE("/admin/bookings/{id}/delete", name ="admin_bookings_delete")
     * @param Booking $booking
     * @return void
     */
    public function delete(Booking $booking)
    {
            $em=$this->getDoctrine()->getManager();
            $em->remove($booking);
            $em->flush();
            // notification
            $this->addFlash(
                "success",
                // avoir acces au "name" par le "booking" avec la methode des fonctions et non variable qu'on a l'habitude d'utiliser
                // "admin/booking/index.html.twig L-30" Pour la methode des variales
                "La reservation de <strong>{$booking->getBooker()->getFullName()}</strong> a bien été supprimé"
            );
        return $this->redirectToRoute('admin_bookings_index');
    }

}

