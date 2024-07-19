<?php

namespace App\Form;

use App\Entity\Ticket;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description', TextareaType::class, [
                'label' => 'Description',
            ])
            ->add('dateStart', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date de début',
            ])
            ->add('dateEnd', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date de fin',
<<<<<<< HEAD
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Open' => 'ouvert',
                    'Closed' => 'resolus',
                ],
                'label' => 'Statut',
=======

>>>>>>> main
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ticket::class,
        ]);
    }
}





