<?php

namespace App\Form;

use App\Entity\Ad;
use App\Form\ImageType;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

//au lieu de "( class_AdType_extends_AbstractType )" on peut l'extends de ("ApplicationType")pour beneficier de la fonction qui y est declarée (getConfiguration) vu que celui ci extends aussi de AbstracType 
class AdType extends ApplicationType
{
   

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, $this->getConfiguration(
                "Titre", "Tapez un super titre pour votre annonce !")
                )

            ->add('slug', TextType::class, $this->getConfiguration(
                "Adresse web", "Tapez l'adresse web !",[
                    'required'=>false
                ] )
                )
                
            ->add('coverImage', UrlType::class, $this->getConfiguration(
                "Url principale", "Tapez un super titre pour votre annonce !")
                )
                
            ->add('introduction', TextType::class, $this->getConfiguration(
                 "Introduction", "Donnez une description globale de l'annonce")
                 )
                    
            ->add('content', TextareaType::class, $this->getConfiguration(
                "Description detaillé", "Tapez une description qui donne vraiment envie de venir chez vous!")
                )
                        
             ->add('rooms', IntegerType::class, $this->getConfiguration(
                 "Nombre de chambre", "Le nombre de chambres disponibles !")
                 )
                            
            ->add('price', MoneyType::class, $this->getConfiguration(
                "Prix par nuit", "Indiquez le prix que vous voulez pour une nuit !")
                )

                //Ajouter un nouveau champ (image) au formulaire de type CollectionType qui nous permet de mettre en place le sous-formulaire
                // elements venant d'un autre entity (ImageType.php)
                // En realité, pour les entrés 'entry_type' le type sera rien d'autres que l'entity d'ou provient les infos du formulaire (ImageType) que nous voulons associer a ce formulaire
            ->add('images',CollectionType::class,[
                'entry_type'=>ImageType::class,
                'allow_add'=>true,
                'allow_delete'=>true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
