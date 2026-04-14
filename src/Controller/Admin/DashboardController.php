<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractController
{
    #[Route('/admin', name: 'admin_root')]
    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    public function redirectToDoctors(): Response
    {
        return $this->redirectToRoute('admin_doctors_index');
    }
}
