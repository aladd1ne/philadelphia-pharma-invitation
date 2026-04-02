<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\RegisteredDoctor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class RegisteredDoctorRegistrationService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function registerFromPayload(array $payload): RegisteredDoctor
    {
        $roomType = isset($payload['room_type']) ? trim((string) $payload['room_type']) : '';

        if (!\in_array($roomType, [RegisteredDoctor::ROOM_SINGLE, RegisteredDoctor::ROOM_DOUBLE], true)) {
            throw new BadRequestHttpException('Invalid room_type.');
        }

        $doctor = new RegisteredDoctor();
        $doctor->setRoomType($roomType);

        if (RegisteredDoctor::ROOM_SINGLE === $roomType) {
            $doctor->setFirstName($this->requireString($payload, 'first_name', 100));
            $doctor->setLastName($this->requireString($payload, 'last_name', 100));
            $doctor->setEmail($this->requireEmail($payload, 'email'));
            $doctor->setPhone($this->requireString($payload, 'phone', 50));
            $doctor->setInstitution($this->optionalString($payload, 'institution', 255));
            $doctor->setNotes($this->optionalString($payload, 'notes', 65535));
        } else {
            $doctor->setParticipant1FirstName($this->requireString($payload, 'p1_first_name', 100));
            $doctor->setParticipant1LastName($this->requireString($payload, 'p1_last_name', 100));
            $doctor->setParticipant1Email($this->requireEmail($payload, 'p1_email'));
            $doctor->setParticipant2FirstName($this->requireString($payload, 'p2_first_name', 100));
            $doctor->setParticipant2LastName($this->requireString($payload, 'p2_last_name', 100));
            $doctor->setParticipant2Email($this->requireEmail($payload, 'p2_email'));
            $doctor->setSharedPhone($this->optionalString($payload, 'double_phone', 50));
            $doctor->setSharedInstitution($this->optionalString($payload, 'double_institution', 255));
            $doctor->setSharedNotes($this->optionalString($payload, 'double_notes', 65535));
        }

        $this->entityManager->persist($doctor);
        $this->entityManager->flush();

        return $doctor;
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function requireString(array $payload, string $key, int $maxLen): string
    {
        $v = isset($payload[$key]) ? trim((string) $payload[$key]) : '';

        if ('' === $v) {
            throw new BadRequestHttpException(sprintf('Le champ « %s » est obligatoire.', $key));
        }

        if (\strlen($v) > $maxLen) {
            throw new BadRequestHttpException(sprintf('Le champ « %s » est trop long.', $key));
        }

        return $v;
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function requireEmail(array $payload, string $key): string
    {
        $v = $this->requireString($payload, $key, 180);

        if (!filter_var($v, \FILTER_VALIDATE_EMAIL)) {
            throw new BadRequestHttpException(sprintf('E-mail invalide pour « %s ».', $key));
        }

        return $v;
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function optionalString(array $payload, string $key, int $maxLen): ?string
    {
        $v = isset($payload[$key]) ? trim((string) $payload[$key]) : '';

        if ('' === $v) {
            return null;
        }

        if (\strlen($v) > $maxLen) {
            throw new BadRequestHttpException(sprintf('Le champ « %s » est trop long.', $key));
        }

        return $v;
    }
}
