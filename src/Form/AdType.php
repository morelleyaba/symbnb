<?php

namespace App\Form;

use App\Entity\Ad;
use App\Form\ImageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class AdType extends AbstractType
{
   /**
    * permet d'avoir la configuration de base d'un champ !
    *
    * @param string $label
    * @param string $placeholder
    * @return array $champ_requis
    * @return array
    */
    // on cree une fonction pour faciliter la tache pour ne pas avoir a repeter le label et le placeholder pour chaque champ
    //Supprimer le fait que le champ "slug" soit required en ajoutant un deuxieme paramettre
    private function getConfiguration($label, $placeholder, $champ_requis=[]){
        return array_merge([
            'label'=>$label,
            'attr'=>[
                'placeholder'=>$placeholder
            ]
            ], $champ_requis);
        }

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
