<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

final class RegistrationConfirmationController extends AbstractController
{
    /** Session key set after successful registration (see IndexController). */
    public const SESSION_FLAG = 'registration_just_completed';

    #[Route('/inscription/merci', name: 'registration_confirmation', methods: ['GET'])]
    public function confirmation(Request $request): Response
    {
        $session = $request->getSession();

        if (true !== $session->get(self::SESSION_FLAG)) {
            return $this->redirectToRoute('home');
        }
        $session->remove(self::SESSION_FLAG);

        $projectDir = (string) $this->getParameter('kernel.project_dir');
        $pdfPath = $projectDir . '/public/assets/docs/programme.pdf';

        return $this->render('registration/confirmation.html.twig', [
            'programPdfAvailable' => is_readable($pdfPath),
        ]);
    }

    #[Route('/telecharger/programme', name: 'download_event_program', methods: ['GET'])]
    public function downloadProgramme(): Response
    {
        $projectDir = (string) $this->getParameter('kernel.project_dir');
        $path = $projectDir . '/public/assets/docs/programme.pdf';

        if (!is_readable($path)) {
            throw new NotFoundHttpException('Le fichier programme n\'est pas disponible pour le moment.');
        }

        return $this->file($path, 'Programme-JCM-2026.pdf', ResponseHeaderBag::DISPOSITION_ATTACHMENT);
    }
}
