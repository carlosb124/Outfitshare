<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\OutfitRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractDashboardController
{
    public function __construct(
        private UserRepository $userRepository,
        private OutfitRepository $outfitRepository,
    ) {}

    #[Route('/admin', name: 'app_admin_dashboard')]
    public function index(): Response
    {
        $countUsers = $this->userRepository->count([]);
        $countOutfits = $this->outfitRepository->count([]);

        return $this->render('admin/dashboard.html.twig', [
            'count_users' => $countUsers,
            'count_outfits' => $countOutfits,
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('OutfitShare Admin');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
    }
}