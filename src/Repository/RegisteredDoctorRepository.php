<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\RegisteredDoctor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RegisteredDoctor>
 */
class RegisteredDoctorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RegisteredDoctor::class);
    }

    /**
     * @return list<RegisteredDoctor>
     */
    public function findAllOrderedByDateDesc(): array
    {
        return $this->createQueryBuilder('r')
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return list<RegisteredDoctor>
     */
    public function findAllMatchingSearch(?string $search): array
    {
        $qb = $this->createOrderedQueryBuilder();
        $this->applySearch($qb, $search);

        return $qb->getQuery()->getResult();
    }

    /**
     * @return array{items: list<RegisteredDoctor>, total: int}
     */
    public function searchPaginated(?string $search, int $page, int $perPage): array
    {
        $page = max(1, $page);
        $perPage = min(100, max(1, $perPage));

        $qb = $this->createOrderedQueryBuilder();
        $this->applySearch($qb, $search);

        $qb->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        $paginator = new Paginator($qb->getQuery(), false);

        return [
            'items' => iterator_to_array($paginator->getIterator(), false),
            'total' => $paginator->count(),
        ];
    }

    private function createOrderedQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('r')
            ->orderBy('r.createdAt', 'DESC');
    }

    private function applySearch(QueryBuilder $qb, ?string $search): void
    {
        $search = null !== $search ? trim($search) : '';

        if ('' === $search) {
            return;
        }

        $like = '%' . str_replace(['%', '_', '\\'], ['\\%', '\\_', '\\\\'], $search) . '%';

        $or = $qb->expr()->orX(
            $qb->expr()->like('r.roomType', ':q'),
            $qb->expr()->like('r.firstName', ':q'),
            $qb->expr()->like('r.lastName', ':q'),
            $qb->expr()->like('r.email', ':q'),
            $qb->expr()->like('r.phone', ':q'),
            $qb->expr()->like('r.institution', ':q'),
            $qb->expr()->like('r.notes', ':q'),
            $qb->expr()->like('r.participant1FirstName', ':q'),
            $qb->expr()->like('r.participant1LastName', ':q'),
            $qb->expr()->like('r.participant1Email', ':q'),
            $qb->expr()->like('r.participant2FirstName', ':q'),
            $qb->expr()->like('r.participant2LastName', ':q'),
            $qb->expr()->like('r.participant2Email', ':q'),
            $qb->expr()->like('r.sharedPhone', ':q'),
            $qb->expr()->like('r.sharedInstitution', ':q'),
            $qb->expr()->like('r.sharedNotes', ':q'),
        );

        if (ctype_digit($search)) {
            $or->add($qb->expr()->eq('r.id', ':rid'));
            $qb->setParameter('rid', (int) $search);
        }

        $qb->andWhere($or)->setParameter('q', $like);
    }
}
