<?php

namespace App\Form;

use App\Entity\Filiere;
use App\Entity\EditFiliere;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditFiliereType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'filiere',
                EntityType::class,
                [
                    "class" => Filiere::class,
                    "choice_label" => "designation",
                    "label" => "choisir une désignation"
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EditFiliere::class,
        ]);
    }
}
