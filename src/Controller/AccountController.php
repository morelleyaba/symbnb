<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class AccountController extends AbstractController
{
    // -------------------Enregistrer un profil d'un auteur / utilisateur--------------------
    /**
     * creation du formulaire d'inscription--------------------------------------
     * @Route("/register", name="account_register")
     */
    public function register(Request $request,UserPasswordEncoderInterface $encoder): Response
    {
        $user=new User();
        
         //Relier le formulaire issu de ["RegistrationType"] a la class de l'entity (User)
        $form=$this->createForm(RegistrationType::class,$user);
        // analyser la requete du formulaire pour relier toutes les informations qui se retrouvent dans le formulaire dans notre variable annonce $user
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            // appeler mon manager;
        $em=$this->getDoctrine()->getManager();
            // On veux acher le mot de pass
            // tout d'abords "$pwd" egale au password obtenu apres saisie,
            //  ensuite "$password" egale au resultat de l'encodage de "$pwd"
            // enfin lors de l'insertion dans la base de donnée, inserer le mot de pass aché ["setPassword($password)"]
        $pwd=$user->getPassword();
        $password=$encoder->encodePassword($user,$pwd);
        $user->setPassword($password);

        $em->persist($user);
        $em->flush();

         // notification
         $this->addFlash(
            "success",
            "Votre compte a bien été creé, vous pouvez maitenant vous connecter"
        );
        return $this->redirectToRoute('app_login');
    }
        return $this->render('account/register.html.twig',[
            'regist_form'=>$form->createView()
        ]);
    }

    // -------------------Modifier mon profil--------------bin/console make:form AccountType------

    /**
     * Permet d'afficher et de traiter le formulaire d'edition de profile
     * @Route("/account/profile", name="account_profile")
     * 
     * securité
     *@IsGranted("ROLE_USER")
     * @return Response
     */
    public function profile(Request $request) {

        //---------------------------ATTENTION------------------------

        // on ne fera pas ($user = new user), ici on veux modifier et non creer,pas donc besoin de creer un nouvelle objet comme on l'a fait lorsqu'on voulait creer un nouelle enreistrement ou annonce
        // on va plutot recuperer dans la variable ("$user"), l'utilisateur qui est connecté, on l'obtient grace au ("$user=$this->getUser()")
            $user=$this->getUser();

            //Relier le formulaire issu de ["AccountType"] a la class de l'entity (User)
        $formProfile=$this->createForm(AccountType::class,$user);
        $formProfile->handleRequest($request);

        if($formProfile->isSubmitted() && $formProfile->isValid()){
            //appeler doctrine
            $em=$this->getDoctrine()->getManager();
            //C'est le 'user' qu'on persiste; l'objet qui a été creé et qu'on veut modifier & l'objet ici c'est le 'user' et nom le "formProfile" 
            $em->persist($user);
            $em->flush();

            // notification
         $this->addFlash(
            "success",
            "Votre profile a bien été modifié"
        );

        } 
        return $this->render("account/profile.html.twig",[
            "formProfile"=>$formProfile->createView()
        ]);
    }

    // --------------------------------------modifier mon mot de pass----------------------------------------

    /**
     * @Route("/account/passwordUpdate", name="account_password")
     * @IsGranted("ROLE_USER")
     * @return Response
     */
     //---------------------------ATTENTION------------------------

        //On va creer une fausse entity ("PasswordUpdate.php" suivie de la creation d'un faux formuliare form "PasswordUpdateType" qui ne sera pas lié a son tour a une entity) pour avoir acces a la validation de symfony,
        // on devra bien ententendu supprimer les annotation de cette fausse entity
        //On ne veux pas que cette entity soit liée a la base de donnée; elle n'est pas la pour xa
        //tout ce qu'on veux une jolie class php pour gerer la modification du mot de pass, pas donc de migration
        public function updatePassword(Request $request,UserPasswordEncoderInterface $encoder){

            //creons un nouvel objet de la class issu de la fausse entity ("PasswordUpdate") issu de la fausse entity ("passwordUpdate.php")
            $passwordUpdate=new PasswordUpdate();
            //Lier la class de la fausse entity ("PasswordUpdate") au formulaire issu de ["PasswordUpdateType"]
            $pwdForm=$this->createForm(PasswordUpdateType::class,$passwordUpdate);
            // analyser la requete du formulaire pour relier toutes les informations
            $pwdForm->handleRequest($request);
            
                // recuperer l'utilisateur connecté / On le cree pas, il existe deja
                $user=$this->getUser();
                // (L-114) recuperer le nouveau mot de pass (de la fausse entity ["PasswordUpdate"]) de l'utilisateur connecté a l'aide de l'accesseur ["getNewPassword"]
                $getNewPwd=$passwordUpdate->getNewPassword();
                // recuperer l'ancien mot de pass qui vient d'etre saisi par l'utilisateur (a l'etat brut, pas encore hashé) dont on veux verifier la veracité / a l'aide de l'acceseur ("getOldPassword") /sans oublier qu'il provient de la class issue de la fausse entity "PasswordUpdate"
                $getAncienPwd=$passwordUpdate->getOldPassword();
                // recuperer le mot de pass de la base de donné de l'utilisateur connecté qu'on veux modifier(qui est deja a l'etat haché) / a l'aide de l'acceseur ("getPassword")/sans oublier qu'il provient de la class issue de la entity "user"
                $userPwd=$user->getPassword();

                        // si le formulaire est valide
                        if ($pwdForm->isSubmitted() && $pwdForm->isValid()) {
                        // ----------------------ATTENTION-----------Il y'a une fausse entity donc on ne va pas executer le nouveau mot de pass comme l'execution normale-------------------------------------
                

                // verifier si l'ancien mot de pass est identique au nouveau mot de pass entré par l'utilisateur
                // avec la fonction "(password_verify)" qui prend en paramettre ["le mot de pass saisi dont on veux verifier la veracité "] && ["le mot de passe a l'interieur de la table qu'on veux modifier(qui est deja haché) "]
                if (!password_verify($getAncienPwd,$userPwd)) {
                    # gerer l'erreur,, accedons au champ de "oldPassword"(champs de saisie de l'ancien mot de pass) pour lui ajouter une erreure grace a ["$pwdForm->get(oldPassword)"]
                    $pwdForm->get('oldPassword')->addError(new FormError("l'ancien mot de pass entré n'est pas correcte"));
                }else {
               
                // Hasher le nouveau mot de pass obtenu / RAPPEL : La fonction "encodePassword" prend en paramettre (l'entity sur laquelle je veux operer et ensuite le mot "mot_de_passe" que je veux encoder )
                $newPwd=$encoder->encodePassword($user,$getNewPwd);
                // Attribuer le nouveau mot de pass encodé "($newPwd)" a la propriété "(password)" de l'entity (user) a l'aide du mutateur "(setPassword)"
                $user->setPassword($newPwd);

                $manager=$this->getDoctrine()->getManager();
                // Demander a manager de persister notre objet "$user" qui vient de recevoir une nouvelle valeur de sa proprieté "Password"
                $manager->persist($user);
                $manager->flush();

                 // notification
         $this->addFlash(
            "success",
            "Votre mot de passe a bien été modifié"
        );
                return $this->redirectToRoute('account_index');
            }
            }


        return $this->render("account/password.html.twig",[
            "pwdForm"=>$pwdForm->createView()
        ]);
     }

}
