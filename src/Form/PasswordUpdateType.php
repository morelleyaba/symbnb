<?php

namespace App\Form;

use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

//au lieu de "( class_AdType_extends_AbstractType )" on peut l'extends de ("ApplicationType")pour beneficier de la fonction qui y est declarée (getConfiguration) vu que celui ci extends aussi de AbstracType 
class PasswordUpdateType extends ApplicationType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('oldPassword',PasswordType::class,$this->getConfiguration(
                'Ancien Mot de pass','Donnez Votre mot de pass Actuel'))
            ->add('newPassword',PasswordType::class,$this->getConfiguration(
                'Nouveau Mot de pass','Tapez Votre nouveeau mot de pass Actuel'))
            ->add('confirmPassword',PasswordType::class,$this->getConfiguration(
                'Confirmation du Mot de pass','Veuillez confirmer Votre mot de pass'));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
