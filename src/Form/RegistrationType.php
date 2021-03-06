<?php

namespace App\Form;

use App\Entity\User;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class RegistrationType extends ApplicationType
{
    

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder 
            ->add('firstName',TextType::class,$this->getConfiguration(
                'Nom', 'Votre Nom'))
            ->add('lastName',TextType::class,$this->getConfiguration(
                'Prenom','Votre Prenom'))
            ->add('email',EmailType::class,$this->getConfiguration(
                'Email','Votre email'))
            ->add('picture',UrlType::class,$this->getConfiguration(
                'Image','L\'Url de votre image'))
            // ->add('roles')
            ->add('password',PasswordType::class,$this->getConfiguration(
                'Mot de pass','Votre mot de pass'))
                // ajoutons notre nouveau champ de confirmation du mot de pass
                ->add('passwordConfirm',PasswordType::class,$this->getConfiguration(
                    'Confirmation du Mot de pass','Veuillez confirmer Votre mot de pass'))
            ->add('introduction',TextType::class,$this->getConfiguration(
                'Introduction','Presentez vous'))
            ->add('description',TextareaType::class,$this->getConfiguration(
                'Desciption','Presentez vous en detail'));
            //le slug va etre generé automatiquement, donc RDV dans l'entity User pour creer le constructeur
            // ->add('slug')
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
