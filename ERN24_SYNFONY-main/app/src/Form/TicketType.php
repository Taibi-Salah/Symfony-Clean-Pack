<?php

namespace App\Form;

use App\Entity\ContactInformation;
use App\Entity\Intervention;
use App\Entity\Stock;
use App\Entity\Ticket;
use App\Entity\User;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketType extends AbstractType
{
//    public function buildForm(FormBuilderInterface $builder, array $options): void
//    {
//        $builder
//            ->add('dateStart')
//            ->add('dateEnd')
//            ->add('user', Entity::class, [
//                'class' => User::class,
//                'choice_label' => 'role',
//                'multiple' => true,
//            ]) // TODO: possibly incorrect
//            ->add('user', Entity::class, [
//                'class' => User::class,
//                'choice_label' => 'email',
//            ])
//            ->add('contactInformation', Entity::class, [
//                'class' => ContactInformation::class,
//                'choice_label' => 'lastName',
//            ])
//            ->add('contactInformation', Entity::class, [
//                'class' => ContactInformation::class,
//                'choice_label' => 'firstName',
//            ])
//            ->add('contactInformation', Entity::class, [
//                'class' => ContactInformation::class,
//                'choice_label' => 'phoneNumber',
//            ])
//            ->add('intervention', Entity::class, [
//                'class' => Intervention::class,
//                'choice_label' => 'label',
//            ])
//            ->add('stock', Entity::class, [
//                'class' => Stock::class,
//                'choice_label' => 'label',
//                'multiple' => true,
//                'expanded' => true,
//            ])
//            ->add('stock', Entity::class, [
//                'class' => Stock::class,
//                'choice_label' => 'referenceNb',
//            ])
//            ->add('stock', Entity::class, [
//                'class' => Stock::class,
//                'choice_label' => 'quantity',
//            ])
//            ->add('Enregistrer', SubmitType::class)
//        ;
//    }
//
//    public function configureOptions(OptionsResolver $resolver): void
//    {
//        $resolver->setDefaults([
//            'data_class' => Ticket::class,
//        ]);
//    }
}