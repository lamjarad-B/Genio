<?php

namespace App\Form;

use App\Entity\Personne;

use Symfony\Component\Form\Extension\Core\Type\FormType;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Validator\Constraints\Length;
class CreateTreeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add(
            // Groupe Proprietaire
            $builder->create('groupe_proprietaire', FormType::class, [
                'label' => 'Vous',
                'mapped' => false,
                'compound' => true,
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'required' => true,
                'constraints' => new Length([
                    'min' => 2,
                    'max' => 30
                ]),
                'attr' => ['placeholder' => 'Nom de naissance'],
                'data' => $options['data']->getNom(),
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'required' => true,
                'constraints' => new Length([
                    'min' => 2,
                    'max' => 30
                ]),
                'data' => $options['data']->getPrenom(),
            ])
                
                ->add('date_naissance', DateType::class, [
                    'label' => 'Date de naissance',
                    'required' => false,
                    'widget' => 'single_text',
                    //'html5' => false,
                    //'format' => 'dd-MM-yyyy',
                    'empty_data' => null
                ])
                ->add('date_deces', DateType::class, [
                    'label' => 'Date de décès',
                    'required' => false,
                    'widget' => 'single_text',
                    //'html5' => false,
                    //'format' => 'dd-MM-yyyy',
                    'empty_data' => null,
                ])
                ->add('lieu_naissance', TextType::class, [
                    'label' => 'Lieu de naissance',
                    'required' => false,
                    'constraints' => new Length([
                        'min' => 2,
                        'max' => 30,
                    ])
                ])
            )
            
            ->add(
                // Groupe père
                $builder->create('groupe_pere', FormType::class, [
                    'label' => 'Père',
                    'mapped' => false,
                    'compound' => true,
                ])
                ->add('nom', TextType::class, [
                    'label' => 'Nom',
                    'required' => true,
                    'constraints' => new Length([
                        'min' => 2,
                        'max' => 30
                    ]),
                    'attr' => ['placeholder' => 'Nom de naissance']
                ])
                ->add('prenom', TextType::class, [
                    'label' => 'Prénom',
                    'required' => true,
                    'constraints' => new Length([
                        'min' => 2,
                        'max' => 30
                    ])
                ])
                
                ->add('date_naissance', DateType::class, [
                    'label' => 'Date de naissance',
                    'required' => false,
                    'widget' => 'single_text',
                    //'html5' => false,
                    //'format' => 'dd-MM-yyyy',
                    'empty_data' => null
                ])
                ->add('date_deces', DateType::class, [
                    'label' => 'Date de décès',
                    'required' => false,
                    'widget' => 'single_text',
                    //'html5' => false,
                    //'format' => 'dd-MM-yyyy',
                    'empty_data' => null,
                ])
                ->add('lieu_naissance', TextType::class, [
                    'label' => 'Lieu de naissance',
                    'required' => false,
                    'constraints' => new Length([
                        'min' => 2,
                        'max' => 30,
                    ])
                ])
            )
            ->add(
                // Groupe père
                $builder->create('groupe_pere', FormType::class, [
                    'label' => 'Père',
                    'mapped' => false,
                    'compound' => true,
                ])
                ->add('nom', TextType::class, [
                    'label' => 'Nom',
                    'required' => true,
                    'constraints' => new Length([
                        'min' => 2,
                        'max' => 30
                    ]),
                    'attr' => ['placeholder' => 'Nom de naissance']
                ])
                ->add('prenom', TextType::class, [
                    'label' => 'Prénom',
                    'required' => true,
                    'constraints' => new Length([
                        'min' => 2,
                        'max' => 30
                    ])
                ])
                
                ->add('date_naissance', DateType::class, [
                    'label' => 'Date de naissance',
                    'required' => false,
                    'widget' => 'single_text',
                    //'html5' => false,
                    //'format' => 'dd-MM-yyyy',
                    'empty_data' => null
                ])
                ->add('date_deces', DateType::class, [
                    'label' => 'Date de décès',
                    'required' => false,
                    'widget' => 'single_text',
                    //'html5' => false,
                    //'format' => 'dd-MM-yyyy',
                    'empty_data' => null,
                ])
                ->add('lieu_naissance', TextType::class, [
                    'label' => 'Lieu de naissance',
                    'required' => false,
                    'constraints' => new Length([
                        'min' => 2,
                        'max' => 30,
                    ])
                ])
            )
            ->add(
                // Groupe mère
                $builder->create('groupe_mere', FormType::class, [
                    'label' => 'Mère',
                    'mapped' => false,
                    'compound' => true,
                ])
                ->add('nom', TextType::class, [
                    'label' => 'Nom',
                    //'mapped' => false,
                    'required' => true,
                    'constraints' => new Length([
                        'min' => 2,
                        'max' => 30
                    ]),
                    'attr' => ['placeholder' => 'Nom de naissance']
                ])
                ->add('prenom', TextType::class, [
                    'label' => 'Prénom',
                    //'mapped' => false,
                    'required' => true,
                    'constraints' => new Length([
                        'min' => 2,
                        'max' => 30
                    ])
                ])
                ->add('date_naissance', DateType::class, [
                    'label' => 'Date de naissance',
                    'required' => false,
                    //'mapped' => false,
                    'widget' => 'single_text',
                    //'html5' => false,
                    //'format' => 'dd-MM-yyyy',
                    'empty_data' => null
                ])
                    
                ->add('date_deces', DateType::class, [
                    'label' => 'Date de décès',
                    'required' => false,
                    //'mapped' => false,
                    'widget' => 'single_text',
                    //'html5' => false,
                    //'format' => 'dd-MM-yyyy',
                    'empty_data' => null,
                ])
                
                ->add('lieu_naissance', TextType::class, [
                    'label' => 'Lieu de naissance',
                    'required' => false,
                    //'mapped' => false,
                    'constraints' => new Length([
                        'min' => 2,
                        'max' => 30
                    ])
                ])
      
            )


            ->add('submit', SubmitType::class, ['label'=> "Commencer mon arbre"])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Personne::class,
        ]);
    }
}
