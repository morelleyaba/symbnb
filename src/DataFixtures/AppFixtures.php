<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
// use Cocur\Slugify\Slugify;
use App\Entity\Image;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
                    // les fixtures permettent de creer de fausses données/remplir la base de donnée avec des fausse info 
                    // au paravant avec php on prenait la peine de le faire manuelement dans phpmyadmin et c'etait epuisant
                    // avec symfony on cree la fixture en important un package de fixture grace a la commande ("composer require orm-fixtures --dev")
    public function load(ObjectManager $manager)
    {
                    // faker est une librairie qui permet de rendre plus realiste nos fausse données 
                    // via la commande ("composer require fzaninotto/faker")
        $faker=Factory::create('fr-Fr');
                    // $slugify = new Slugify();
        for($i=1; $i<=30; $i++){

                    // crer une variable $title qui demande a faker de generer un mot au hazar,
                    // on aura donc du text au hazar dans notre title grace a la fonction (sentence)
            $title=$faker->sentence();
            // faker nous permet egalement d'avoir des URL d'image grace a la methode (imageURL)
            $coverImage=$faker->imageURL(1000,350);
            $introduction=$faker->paragraph(2);
            $content= '<p>'.join('<p></p>',$faker->paragraphs(5)).'</p>' ;

                    // methode slugify qui prend en parametre la variable $title (text alt des image separer par des tirets)
                    //  $slug=$slugify->slugify($title); (voir Ad.php Ligne 63)
                    // creer une nouvelle annonce (objet $ad)
        $ad=new Ad();
        $ad->setTitle($title)
                    // ->setSlug($slug)
            ->setPrice(mt_rand(40,200))
            ->setIntroduction($introduction)
            ->setContent($content)
            ->setCoverImage($coverImage)
            ->setRooms(mt_rand(1,9));
       
    // __________________________
            for($j=1; $j<=mt_rand(2,5); $j++){
                $image= new Image();
                 $image ->setUrl($faker->imageUrl())
                        ->setCaption($faker->sentence())
                        ->setAd($ad);

                 $manager->persist($image);
            }
    // __________________________

                    // demander a doctrine de faire persister mes requetes
            $manager->persist($ad);
        }
                    // si on mettait l'instruction de (flush()) avant de fermer la parenthese xa allait 
                    // crer plusieur objets, donc la on demande a doctrine de faire persister les requettes
                    //  grace a (persist()) et ensuite on demande (flush()) d'executer les requetes gardée par doctrine vers la base de donnée
            
                    // executer/enregister une requete en symfony a la base de donnée pour creer mes annonces
             $manager->flush();
                    // $product = new Product();
                    // $manager->persist($product);

                    // enfin ouvrir la commande et relancer la commande pour
                    //  valider l'execution de la fixture via ("php bin/console doctrine:fixtures:load")

                    //   meme apres avoir utilisé (faker) pour rendre realiste mes fausses donnée de fixtures,
                    //  faille lancer l'execution de fixure via ("php bin/console doctrine:fixtures:load") pour metre a jour la base de donnée avec nos nouvelles données

    }
}
