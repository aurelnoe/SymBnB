<?php

namespace App\Form;

use App\Entity\User;
use App\Form\ApplicationType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, $this->getConfiguration('Nom',"Ajouter votre nom"))
            ->add('lastName', TextType::class, $this->getConfiguration('Prénom',"Ajouter votre prénom"))
            ->add('picture', UrlType::class, $this->getConfiguration('Photo de profil',"Ajouter une photo"))
            ->add('introduction', TextType::class, $this->getConfiguration('Introduction',"Présentez vous en quelques mots ..."))
            ->add('description', TextareaType::class, $this->getConfiguration('Description détaillée',"Ajouter une description détaillée"))
            ->add('email', EmailType::class, $this->getConfiguration('Adresse email',"Ajouter votre adresse email"))
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Le mot de passe doit être identique.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => [
                    'label' => 'Mot de passe',
                    'attr' => ['placeholder' => 'Mot de passe']],
                'second_options' => [
                    'label' => 'Confirmation mot de passe',
                    'attr' => ['placeholder' => 'Confirmation mot de passe']],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
