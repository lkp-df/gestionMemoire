<?php

namespace App\Form;

use App\Entity\Filiere;
use App\Entity\Encadreur;
use App\Entity\EtudiantMemoire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormEtudiantMemoireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('prenom')
            ->add('adresse')
            ->add('theme')
            ->add('annee')
            ->add('options')
            ->add(
                'filiere',
                EntityType::class,
                [
                    "class" => Filiere::class,
                    "choice_label" => "designation",
                    "label"=>"choisir la filiere de l'etudiant"
                ]
            )
            ->add(
                'encadreur',
                EntityType::class,
                [
                    "class" => Encadreur::class,
                    "choice_label" => "nom",
                    "label"=>"choisir l'encadreur"
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EtudiantMemoire::class,
        ]);
    }
}
