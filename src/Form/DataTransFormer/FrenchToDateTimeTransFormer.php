<?php 
namespace App\Form\DataTransFormer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

// _________________________________Les DataTransformer pour transformer les données au moment ou les passe au formulaire ou le formulaire nous les passe__

class FrenchToDateTimeTransFormer implements DataTransformerInterface {

    // en recevant une vrai date 'DateTime'(php),la fonction "transform()" nous renvoie une date au format francais
    public function transform($date){

        if ($date===null) {
           return '';
        }
        return $date->format('d/m/Y');
    }

    // recoit une date au format francais (depuis l'interface de reservation), la fonction reverseTransform() nous renvoie un vrai dateTime
    public function reverseTransform($frenchDate){
        //on enttend par frenchDate = 21/09/2018
        // si la date fournie est vide
        if ($frenchDate===null) {
            // message de l'Exeption...et non le message qui sera afficher a l'utilisateur
            throw new TransformationFailedException("Vous devez fournir une date !");
         }
         //creer une date a partir de ma variable "$frenchDate" recu
            $reverseDate= \DateTime ::createFromFormat('d/m/Y',$frenchDate);

            // la fonction "createFromFormat()" renvoiera false si elle n'arrive pas a creer une date 
            // example: on lui dit que la "$frenchDate" qu'il recoit est sous forme ("d/m/Y") lors de la saisie a la reservation, on envoyer une date sous la forme ("d-m/Y"), dans ce cas on aura une erreure
            if ($reverseDate===false) {
                # message de l'Exeption...
                throw new TransformationFailedException("Le format de la date n'est pas le bon !");
            }

            return $reverseDate;
    }
}
?>