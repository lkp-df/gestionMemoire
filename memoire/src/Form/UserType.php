<?php

namespace App\Form;

use App\Entity\EditUser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            // ->add('email')
            ->add(
                'roles',
                ChoiceType::class,
                [
                    "choices" =>
                    [
                        "Sécrétaire" => "ROLE_SECRETAIRE",
                        "Directeur" => "ROLE_DIRECTEUR",
                        "Administrateur" => "ROLE_ADMIN"
                    ],
                    "multiple"=>true,
                    "expanded"=>true
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EditUser::class,
        ]);
    }
}
