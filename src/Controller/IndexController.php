<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\RegisteredDoctorRegistrationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

final class IndexController extends AbstractController
{
    public function __construct(
        private readonly RegisteredDoctorRegistrationService $registrationService,
        private readonly CsrfTokenManagerInterface $csrfTokenManager,
    ) {
    }

    #[Route('/', name: 'home', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $data = $request->request->all();
            $token = (string) ($data['_token'] ?? '');
            if (!$this->csrfTokenManager->isTokenValid(new CsrfToken('registration', $token))) {
                $this->addFlash('danger', 'Jeton CSRF invalide. Veuillez recharger la page et réessayer.');

                return $this->redirectToRoute('home');
            }

            unset($data['_token']);

            try {
                $this->registrationService->registerFromPayload($data);
            } catch (BadRequestHttpException $e) {
                $this->addFlash('danger', $e->getMessage());

                return $this->redirect($this->generateUrl('home').'#register');
            }

            $request->getSession()->set(RegistrationConfirmationController::SESSION_FLAG, true);

            return $this->redirectToRoute('registration_confirmation');
        }

        return $this->render('base.html.twig');
    }
}
