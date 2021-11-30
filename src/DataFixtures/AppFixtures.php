<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
// use Cocur\Slugify\Slugify;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Image;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{       private $encoder;
        public function __construct(UserPasswordEncoderInterface $encoder)
        {
                $this->encoder=$encoder;
        }
              
                    // les fixtures permettent de creer de fausses données/remplir la base de donnée avec des fausse info 
                    // au paravant avec php on prenait la peine de le faire manuelement dans phpmyadmin et c'etait epuisant
                    // avec symfony on cree la fixture en important un package de fixture grace a la commande ("composer require orm-fixtures --dev")
    public function load(ObjectManager $manager)
    {
             // faker est une librairie qui permet de rendre plus realiste nos fausse données 
                    // via la commande ("composer require fzaninotto/faker")
        $faker=Factory::create('fr-Fr');

        // __________________________Nous gerons les rolres___________________
        $adminRole=new Role();
        $adminRole->setTitle("ROLE_ADMIN");
        $manager->persist($adminRole);

        // petit test , inserer un utilisateur qui a le role admin_________________________
        // commencons par creer un nouvel utilisateur
        $adminUser=new User();
        // L'objet "$adminUser" sur lequel on veux agir rapidement, accompagné du mot de pass encodé
        $pwd=$this->encoder->encodePassword($adminUser,'jjjjjjjjjj');
        $adminUser->setFirstName("Josiane")
                  ->setLastName("Akre")
                  ->setEmail("josiane@gmail.com")
                  ->setPassword($pwd)
                  ->setPicture("https://randomuser.me/api/portraits/women/43.jpg")
                  ->setIntroduction($faker->sentence)
                  ->setDescription('<p>'.join('<p></p>',$faker->paragraphs(2)).'</p>')
                //   pas de "setUserRole" mais plutot "addUserRole" (aller voir que dans l'entity "user" c'est plutot la fonction "addUserRole()" pour parler de la proprieté userRole qui est de type relation)
                //   attribuer le role d'administrateur a l'utilisateur qui vient d'etre creé
                  ->addUserRole($adminRole);
                //   demander a manager de persister "$adminUser" (le nouvel objet creé) affecté de ses infos 
                  $manager->persist($adminUser);

        //---------------------------Nous gerons les utilisateurs-------------------
        $user=[];
        $genres=['male','female'];
        for ($i=1; $i <= 5; $i++) { 
               $user=new User();
               //Pour chaque utilisateur on va choisir le genre que c'est
               $genre=$faker->randomElement($genres);
               $picture = "https://randomuser.me/api/portraits/";
               //choisir au hazard un element du tableau (genre)
               $pictureId=$faker->numberBetween(1,99).".jpg";
                //  si l'element choisi est "male" alors ajouter "men" dans le cas contraire ajouter "women"
               $picture .=($genre == 'male'? 'men/' : 'women/').$pictureId;

               //(encodePassword)prends en paramettre 2 paramettre [l'entity sur laquelle je veux operer et ensuite le mot "mot_de_passe" que je veux encoder]
               $password=$this->encoder->encodePassword($user,'mot_de_passe');

               $user->setFirstName($faker->firstname)
                    ->setLastName($faker->lastname)
                    ->setEmail($faker->email)
                    ->setPassword($password)
                    ->setPicture($picture)
                    ->setIntroduction($faker->sentence)
                    ->setDescription('<p>'.join('<p></p>',$faker->paragraphs(2)).'</p>');
                    
                    $manager->persist($user);
                    //a la fin de notre boucle, on a 10 Utilisateurs qui seront crées
                    $users[]=$user;
        }


        //---------------------------Between(1,99).".jpg";
                    // $slugify = new Slugify();
        for($i=1; $i<=12; $i++){
                // creer une nouvelle annonce (objet $ad)
                $ad=new Ad();
                    // crer une variable $title qui demande a faker de generer un mot au hazar,
                    // on aura donc du text au hazar dans notre title grace a la fonction (sentence)
            $title=$faker->sentence();
            // faker nous permet egalement d'avoir des URL d'image grace a la methode (imageURL)
            $coverImage=$faker->imageURL(1000,350);
            $introduction=$faker->paragraph(2);
            $content= '<p>'.join('<p></p>',$faker->paragraphs(3)).'</p>' ;

//----------------------Pour chaque annnces on va selectinner un autheur au hazar-------pour 30 annonces il est probables qu'on est plusieus annonces crées par un utilisateur
                
//--------chopper un des utilisateur parmis les 5 crées plus haut-----------
        $user=$users[mt_rand(0, count($users)-1)]; 

//----------------------------------------------
                    // methode slugify qui prend en parametre la variable $title (text alt des image separer par des tirets)
                    //  $slug=$slugify->slugify($title); (voir Ad.php Ligne 63)
                    
        $ad->setTitle($title)
                    // ->setSlug($slug)
            ->setPrice(mt_rand(40,200))
            ->setIntroduction($introduction)
            ->setContent($content)
            ->setCoverImage($coverImage)
            ->setRooms(mt_rand(1,9))
            //Le champs "author" a ete ajouté comme nouveau champs a l'entity "ad" avec pour type "relation"; xa represente en realité notre cle etrangere "id_author" qui apparait dans la table "ad" et si jamais n doit l'utiliser dansle code on dira "setAuthor" qui est une proprieté de "ad" si on veux faire reference a "id_author" NB:le id_author n'est rien d'autre que le "id_user"  
            // Indiquer lors de l'envoie des informations dans "ad" que l'autheur c'est ce utilisateur la (fixture)
            ->setAuthor($user);
       
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
