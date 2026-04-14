<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\RegisteredDoctorRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RegisteredDoctorRepository::class)]
#[ORM\HasLifecycleCallbacks]
class RegisteredDoctor
{
    public const ROOM_SINGLE = 'single';

    public const ROOM_DOUBLE = 'double';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10)]
    private string $roomType = self::ROOM_SINGLE;

    /** Single-room participant */
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $firstName = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $lastName = null;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $institution = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    /** Double room — participant 1 */
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $participant1FirstName = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $participant1LastName = null;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $participant1Email = null;

    /** Double room — participant 2 */
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $participant2FirstName = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $participant2LastName = null;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $participant2Email = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $sharedPhone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sharedInstitution = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sharedNotes = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRoomType(): string
    {
        return $this->roomType;
    }

    public function setRoomType(string $roomType): static
    {
        $this->roomType = $roomType;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getInstitution(): ?string
    {
        return $this->institution;
    }

    public function setInstitution(?string $institution): static
    {
        $this->institution = $institution;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;

        return $this;
    }

    public function getParticipant1FirstName(): ?string
    {
        return $this->participant1FirstName;
    }

    public function setParticipant1FirstName(?string $participant1FirstName): static
    {
        $this->participant1FirstName = $participant1FirstName;

        return $this;
    }

    public function getParticipant1LastName(): ?string
    {
        return $this->participant1LastName;
    }

    public function setParticipant1LastName(?string $participant1LastName): static
    {
        $this->participant1LastName = $participant1LastName;

        return $this;
    }

    public function getParticipant1Email(): ?string
    {
        return $this->participant1Email;
    }

    public function setParticipant1Email(?string $participant1Email): static
    {
        $this->participant1Email = $participant1Email;

        return $this;
    }

    public function getParticipant2FirstName(): ?string
    {
        return $this->participant2FirstName;
    }

    public function setParticipant2FirstName(?string $participant2FirstName): static
    {
        $this->participant2FirstName = $participant2FirstName;

        return $this;
    }

    public function getParticipant2LastName(): ?string
    {
        return $this->participant2LastName;
    }

    public function setParticipant2LastName(?string $participant2LastName): static
    {
        $this->participant2LastName = $participant2LastName;

        return $this;
    }

    public function getParticipant2Email(): ?string
    {
        return $this->participant2Email;
    }

    public function setParticipant2Email(?string $participant2Email): static
    {
        $this->participant2Email = $participant2Email;

        return $this;
    }

    public function getSharedPhone(): ?string
    {
        return $this->sharedPhone;
    }

    public function setSharedPhone(?string $sharedPhone): static
    {
        $this->sharedPhone = $sharedPhone;

        return $this;
    }

    public function getSharedInstitution(): ?string
    {
        return $this->sharedInstitution;
    }

    public function setSharedInstitution(?string $sharedInstitution): static
    {
        $this->sharedInstitution = $sharedInstitution;

        return $this;
    }

    public function getSharedNotes(): ?string
    {
        return $this->sharedNotes;
    }

    public function setSharedNotes(?string $sharedNotes): static
    {
        $this->sharedNotes = $sharedNotes;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $now = new DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }

    /** Display label for list (primary contact name or P1 + P2). */
    public function getDisplayName(): string
    {
        if (self::ROOM_DOUBLE === $this->roomType) {
            $a = trim(($this->participant1FirstName ?? '') . ' ' . ($this->participant1LastName ?? ''));
            $b = trim(($this->participant2FirstName ?? '') . ' ' . ($this->participant2LastName ?? ''));

            return trim($a . ' & ' . $b) ?: '—';
        }

        return trim(($this->firstName ?? '') . ' ' . ($this->lastName ?? '')) ?: '—';
    }
}
