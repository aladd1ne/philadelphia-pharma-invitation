<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Repository\RegisteredDoctorRepository;
use App\Service\RegisteredDoctorApiSerializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class RegisteredDoctorListApiController extends AbstractController
{
    public function __construct(
        private readonly RegisteredDoctorRepository $repository,
        private readonly RegisteredDoctorApiSerializer $serializer,
    ) {
    }

    #[Route('/registered-doctors', name: 'registered_doctors_list', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(Request $request): JsonResponse
    {
        $q = $request->query->get('q');
        $search = trim((string) ($q ?? ''));
        $search = $search === '' ? null : $search;

        $page = max(1, (int) $request->query->get('page', 1));
        $perPage = min(100, max(1, (int) $request->query->get('per_page', 15)));

        $result = $this->repository->searchPaginated($search, $page, $perPage);
        $total = $result['total'];
        $pages = $perPage > 0 ? (int) max(1, (int) ceil($total / $perPage)) : 1;

        return $this->json([
            'items' => $this->serializer->serializeList($result['items']),
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'pages' => $pages,
        ]);
    }
}
