<?php

namespace App\Form;

use App\Entity\Comment;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CommentType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('createdAt')
            ->add('rating',IntegerType::class,$this->getConfiguration('votre note sur 5','Indiquer une note de 0 a 5',[
                'attr'=>[
                    'min'=>0,
                    'max'=>5,
                    'step'=>1
                ]
            ]))
            ->add('content',TextareaType::class,$this->getConfiguration('votre avis','laisser un commentaire'))
            // ->add('ad')
            // ->add('author')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
