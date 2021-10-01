<?php

namespace App\Form;

use App\Entity\EditEncadreur;
use App\Entity\Encadreur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditEncadreurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'encadreur',
                EntityType::class,
                [
                    "class" => Encadreur::class,
                    "choice_label" => "nom",
                    "label"=>"veuillez choisir un encadreur"
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EditEncadreur::class,
        ]);
    }
}
