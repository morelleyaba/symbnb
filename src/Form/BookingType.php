<?php

namespace App\Form;

use App\Entity\Booking;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class BookingType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // ajouter le widget afin que l'affichage du champ de la date soit plus classique
        $builder
            ->add('startDate',DateType::class,$this->getConfiguration("La date d'arrivée","la date a laquelle vous comptez arriver",["widget"=>"single_text"]))
            ->add('endDate',DateType::class,$this->getConfiguration("La date de fin","la date a laquelle vous quitter les lieux",["widget"=>"single_text"]))
            // ->add('createdAt') nous allons faire en sorte que la date de creation s'ajoute seul dans l'entity Booking
            // ->add('amount') pareil pour le prix
            // ->add('booker') l'auteur de la reservation va s'enregistrer seul voir le controller de booking
            // ->add('ad') pareil pour l'annonce
            ->add('comment',TextareaType::class,$this->getConfiguration(false,"Si vous avez un commentaire,n'hesitez pas a en faire part !"))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
