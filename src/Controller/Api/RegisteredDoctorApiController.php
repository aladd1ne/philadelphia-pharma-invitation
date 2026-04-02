<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Service\RegisteredDoctorRegistrationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

final class RegisteredDoctorApiController extends AbstractController
{
    public function __construct(
        private readonly RegisteredDoctorRegistrationService $registrationService,
        private readonly CsrfTokenManagerInterface $csrfTokenManager,
    ) {
    }

    #[Route('/api/registered-doctors', name: 'api_registered_doctors_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = $this->parseBody($request);
        $token = $request->headers->get('X-CSRF-TOKEN') ?? ($data['_token'] ?? '');

        if (!$this->csrfTokenManager->isTokenValid(new CsrfToken('registration', (string) $token))) {
            return new JsonResponse(['error' => 'Jeton CSRF invalide.'], Response::HTTP_FORBIDDEN);
        }

        try {
            $doctor = $this->registrationService->registerFromPayload($data);
        } catch (BadRequestHttpException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse([
            'id' => $doctor->getId(),
            'message' => 'Inscription enregistrée.',
        ], Response::HTTP_CREATED);
    }

    /**
     * @return array<string, mixed>
     */
    private function parseBody(Request $request): array
    {
        $contentType = (string) $request->headers->get('Content-Type', '');

        if (str_contains($contentType, 'application/json')) {
            $raw = $request->getContent();
            $decoded = json_decode($raw, true);

            return \is_array($decoded) ? $decoded : [];
        }

        return $request->request->all();
    }
}
