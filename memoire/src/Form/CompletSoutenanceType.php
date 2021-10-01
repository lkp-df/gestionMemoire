<?php

namespace App\Form;

use App\Entity\Etudiant;
use App\Entity\Encadreur;
use App\Entity\CompletSoutenance;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class CompletSoutenanceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('salle')
            ->add(
                'typeSoutenance',
                ChoiceType::class,
                [
                    "placeholder" => "choisir type soutenance",
                    "choices" => [
                        "DTS" => "DTS",
                        "LICENCE" => "LICENCE",
                        "MASTER" => "MASTER"
                    ]
                ]
            )
            ->add(
                'date',
                DateType::class,
                [
                    "widget"=>"single_text",
                    "label"=>"Date de soutenance"
                ]
                );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CompletSoutenance::class,
        ]);
    }
}
