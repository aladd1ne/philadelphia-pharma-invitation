<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\RegisteredDoctor;

final class RegisteredDoctorApiSerializer
{
    /**
     * @return array<string, mixed>
     */
    public function serialize(RegisteredDoctor $doctor): array
    {
        return [
            'id' => $doctor->getId(),
            'roomType' => $doctor->getRoomType(),
            'firstName' => $doctor->getFirstName(),
            'lastName' => $doctor->getLastName(),
            'email' => $doctor->getEmail(),
            'phone' => $doctor->getPhone(),
            'institution' => $doctor->getInstitution(),
            'notes' => $doctor->getNotes(),
            'participant1FirstName' => $doctor->getParticipant1FirstName(),
            'participant1LastName' => $doctor->getParticipant1LastName(),
            'participant1Email' => $doctor->getParticipant1Email(),
            'participant2FirstName' => $doctor->getParticipant2FirstName(),
            'participant2LastName' => $doctor->getParticipant2LastName(),
            'participant2Email' => $doctor->getParticipant2Email(),
            'sharedPhone' => $doctor->getSharedPhone(),
            'sharedInstitution' => $doctor->getSharedInstitution(),
            'sharedNotes' => $doctor->getSharedNotes(),
            'displayName' => $doctor->getDisplayName(),
            'createdAt' => $doctor->getCreatedAt()?->format(\DateTimeInterface::ATOM),
            'updatedAt' => $doctor->getUpdatedAt()?->format(\DateTimeInterface::ATOM),
        ];
    }

    /**
     * @param list<RegisteredDoctor> $doctors
     *
     * @return list<array<string, mixed>>
     */
    public function serializeList(array $doctors): array
    {
        return array_map(fn (RegisteredDoctor $d) => $this->serialize($d), $doctors);
    }
}
