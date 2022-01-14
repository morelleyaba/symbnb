<?php

namespace App\Form;

use App\Entity\Ad;
use App\Entity\User;
use App\Entity\Booking;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class AdminBookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startDate',DateType::class,[
                'widget'=>'single_text'
            ])
            ->add('endDate',DateType::class,[
                'widget'=>'single_text'
            ])
            // ->add('createdAt')
            // ->add('amount')
            ->add('comment')
            // ->add('slug')
            // _________________________________________________D14-V20___liste de choix a l'aide des (booker_id et ad_id [cle etrangere] grace au type de champ 'EntityType')
            // Remarquer ici que ces deux dernier champs sont des relations(booker et ad)
            ->add('booker',EntityType::class,[
                'class'=> User::class,
                // 'choice_label'=>'fullName' / On peut aussi proceder comme tels :
                'choice_label'=>function($user){
                    return $user->getFirstName()." ".strtoupper($user->getLastName());
                }
            ])
            ->add('ad',EntityType::class,[
                'class'=> Ad::class,
                'choice_label'=>'title'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
