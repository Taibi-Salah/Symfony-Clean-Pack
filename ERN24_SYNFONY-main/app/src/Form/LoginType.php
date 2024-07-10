<?php

// src/Form/LoginType.php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => ['placeholder' => 'Your email'],
            ])
            ->add('password', PasswordType::class, [
<<<<<<< HEAD
                'label' => 'Password', 
            ])
            ->add('login', SubmitType::class, [
                'label' => 'Se connecter',
=======
                'label' => 'Password',
                'attr' => ['placeholder' => 'Your password'],
>>>>>>> b1f2385 ([/LOGIN] done , ADMIN/USER fixture done)
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}


