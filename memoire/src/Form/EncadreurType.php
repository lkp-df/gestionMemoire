<?php

namespace App\Form;

use App\Entity\Encadreur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EncadreurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('prenom')
            ->add('titre')
            ->add(
                'avatar',
                FileType::class,
                [   
                    "mapped"=>true,
                    "data_class"=>null,
                    "label" => "Profil"
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Encadreur::class,
        ]);
    }
}
