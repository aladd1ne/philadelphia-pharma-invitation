<?php

declare(strict_types=1);

namespace App\Dto;

use App\Entity\RegisteredDoctor;

/**
 * Paginated slice of registered doctors for UI and API.
 */
final class RegisteredDoctorPage
{
    /**
     * @param list<RegisteredDoctor> $items
     */
    public function __construct(
        public readonly array $items,
        public readonly int $total,
        public readonly int $page,
        public readonly int $perPage,
        public readonly ?string $search = null,
    ) {
    }

    public function getTotalPages(): int
    {
        if ($this->perPage < 1) {
            return 1;
        }

        return (int) max(1, (int) ceil($this->total / $this->perPage));
    }
}
