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
            ->add('nom', TextType::class, [
            'required' => true,
            'label' => false,
            'constraints' => new length([
            'min' => 2,
            'max' => 30
            ]),
            'attr' => ['placeholder' => 'Nom de famille']])
            ->add('prenom', TextType::class, [
            'required' => false,
            'label' => false,
            'constraints' => new length([
            'min' => 2,
            'max' => 30
            ]),
            'attr' => ['placeholder' => 'Prénom']])
            
            ->add('date_naissance', TextType::class, [
            'required' => false,
            'label' => false,
            'attr' => ['placeholder' => 'Date de Naissance']])
            

            ->add('nomConjoint', TextType::class, [
            'required' => false,
            'label' => false,
            'mapped' => false,
            'constraints' => new length([
            'min' => 2,
            'max' => 30
            ]),
            'attr' => ['placeholder' => 'Nom du conjoint(e']])
            ->add('prenomConjoint', TextType::class, [
            'required' => false,
            'label' => false,
            'mapped' => false,
            'constraints' => new length([
            'min' => 2,
            'max' => 30
            ]),
            'attr' => ['placeholder' => 'Prénom du conjoint(e)']])
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
