<?php

namespace App\Form;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

use Symfony\Component\Validator\Constraints\Length;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prenom', TextType::class, ['label'=> 'Votre prénom',
            'constraints' => new length([
            'min' => 2,
            'max' => 30
            ]),
            'attr' => ['placeholder' => 'Merci de renseigner votre prénom svp']]) 
            ->add('nom', TextType::class, ['label'=> 'Votre nom',
                'constraints' => new length([
                'min' => 2,
                'max' => 30
                ]),
                'attr' => ['placeholder' => 'Merci de renseigner votre nom svp']]) 
            ->add('email', EmailType::class, ['label'=> 'Votre email',
                'attr' => ['placeholder' => 'Merci de renseigner votre email svp']]) 
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Le mot de passe et la confirmation doivent être identiques',
                'required' => true,
                'first_options' =>['label'=> 'Votre mot ce passe', 'attr' => ['placeholder' => 'Merci de renseigner votre mot de passe svp']],
                'second_options' =>['label'=> 'Confirmer votre mot de passe', 'attr' => ['placeholder' => 'Merci de confirmer votre mot de passe svp']]])
            
            

            ->add('date_naissance', DateType::class, [
                'widget' => 'single_text',])
            //->add('date_deces')
            ->add('sexe', ChoiceType::class, [
                'choices' => [
                    'M' => 'M',
                    'F' => 'F',
                ],
                'expanded' => true,
            ])
          

            ->add('submit', SubmitType::class, ['label'=> "S'inscrire"])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
