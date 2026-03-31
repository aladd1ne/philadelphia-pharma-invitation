<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\RegisteredDoctor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class RegisteredDoctorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('roomType', ChoiceType::class, [
                'label' => 'Type d\'hébergement',
                'choices' => [
                    'Chambre simple' => RegisteredDoctor::ROOM_SINGLE,
                    'Chambre double' => RegisteredDoctor::ROOM_DOUBLE,
                ],
            ])
            ->add('firstName', TextType::class, ['label' => 'Prénom', 'required' => false])
            ->add('lastName', TextType::class, ['label' => 'Nom', 'required' => false])
            ->add('email', EmailType::class, ['label' => 'E-mail', 'required' => false])
            ->add('phone', TextType::class, ['label' => 'Téléphone', 'required' => false])
            ->add('institution', TextType::class, ['label' => 'Établissement / Service', 'required' => false])
            ->add('notes', TextareaType::class, ['label' => 'Demandes particulières', 'required' => false])
            ->add('participant1FirstName', TextType::class, ['label' => 'Participant 1 — Prénom', 'required' => false])
            ->add('participant1LastName', TextType::class, ['label' => 'Participant 1 — Nom', 'required' => false])
            ->add('participant1Email', EmailType::class, ['label' => 'Participant 1 — E-mail', 'required' => false])
            ->add('participant2FirstName', TextType::class, ['label' => 'Participant 2 — Prénom', 'required' => false])
            ->add('participant2LastName', TextType::class, ['label' => 'Participant 2 — Nom', 'required' => false])
            ->add('participant2Email', EmailType::class, ['label' => 'Participant 2 — E-mail', 'required' => false])
            ->add('sharedPhone', TextType::class, ['label' => 'Téléphone (commun)', 'required' => false])
            ->add('sharedInstitution', TextType::class, ['label' => 'Établissement (double)', 'required' => false])
            ->add('sharedNotes', TextareaType::class, ['label' => 'Notes (double)', 'required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RegisteredDoctor::class,
        ]);
    }
}
