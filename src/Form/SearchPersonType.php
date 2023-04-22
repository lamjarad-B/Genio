<?php

namespace App\Form;

use App\Entity\Personne;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Validator\Constraints\Length;
class SearchPersonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, ['label'=> 'Nom de famille',
            'required' => true,
            'constraints' => new length([
            'min' => 2,
            'max' => 30
            ]),
            'attr' => ['placeholder' => 'Merci de renseigner le nom svp']])
            ->add('prenom', TextType::class, ['label'=> 'Prénom',
            'required' => false,
            'constraints' => new length([
            'min' => 2,
            'max' => 30
            ]),
            'attr' => ['placeholder' => 'Merci de renseigner le prénom svp']])
            
            ->add('date_naissance', TextType::class, ['label'=> 'Date de naissance',
            'required' => false])

            // ->add('nom', TextType::class, ['label'=> 'Nom du conjoint(e)',
            // 'required' => false,
            // 'constraints' => new length([
            // 'min' => 2,
            // 'max' => 30
            // ]),
            // 'attr' => ['placeholder' => 'Merci de renseigner le nom du conjoint(e)']])
            // ->add('prenom', TextType::class, ['label'=> 'Prénom du conjoint(e)',
            // 'required' => false,
            // 'constraints' => new length([
            // 'min' => 2,
            // 'max' => 30
            // ]),
            // 'attr' => ['placeholder' => 'Merci de renseigner le prénom du conjoint(e)']])
            // ->add('date_deces')
            // ->add('sexe')
            ->add('submit', SubmitType::class, ['label'=> "Rechercher"])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Personne::class,
        ]);
    }
}
