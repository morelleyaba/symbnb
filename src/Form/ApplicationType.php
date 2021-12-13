<?php 

namespace App\Form;
use Symfony\Component\Form\AbstractType;



class ApplicationType extends AbstractType{

/**
    * permet d'avoir la configuration de base d'un champ !
    * @param string $label
    * @param string $placeholder
    * @return array $champ_requis
    * @return array
    */
    
    protected function getConfiguration($label, $placeholder, $champ_requis=[]){
        return array_merge_recursive([
            'label'=>$label,
            'attr'=>[
                'placeholder'=>$placeholder
            ]
            ], $champ_requis);
        }

}
?>