<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Dto\RegisteredDoctorPage;
use App\Entity\RegisteredDoctor;
use App\Form\RegisteredDoctorType;
use App\Repository\RegisteredDoctorRepository;
use App\Service\RegisteredDoctorExporter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/doctors')]
final class RegisteredDoctorAdminController extends AbstractController
{
    private const PER_PAGE = 15;

    public function __construct(
        private readonly RegisteredDoctorRepository $repository,
        private readonly EntityManagerInterface $entityManager,
        private readonly RegisteredDoctorExporter $exporter,
    ) {
    }

    #[Route('', name: 'admin_doctors_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $q = $request->query->get('q');
        $search = trim((string) ($q ?? ''));
        $search = '' === $search ? null : $search;

        $page = max(1, (int) $request->query->get('page', 1));
        $result = $this->repository->searchPaginated($search, $page, self::PER_PAGE);

        $doctorPage = new RegisteredDoctorPage(
            $result['items'],
            $result['total'],
            $page,
            self::PER_PAGE,
            $search,
        );

        return $this->render('admin/doctor/index.html.twig', [
            'doctor_page' => $doctorPage,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_doctors_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, RegisteredDoctor $doctor): Response
    {
        $form = $this->createForm(RegisteredDoctorType::class, $doctor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->addFlash('success', 'Fiche mise à jour.');

            return $this->redirectToRoute('admin_doctors_index');
        }

        return $this->render('admin/doctor/edit.html.twig', [
            'doctor' => $doctor,
            'form' => $form,
        ]);
    }

    #[Route('/export.xlsx', name: 'admin_doctors_export', methods: ['GET'])]
    public function export(Request $request): Response
    {
        $q = $request->query->get('q');
        $search = trim((string) ($q ?? ''));
        $search = '' === $search ? null : $search;

        return $this->exporter->createStreamedResponse($search);
    }
}
