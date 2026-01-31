<?php

namespace App\Controller\Admin;

use App\Entity\Outfit;
use App\Entity\Prenda;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private \App\Repository\UserRepository $userRepository,
        private \App\Repository\OutfitRepository $outfitRepository
    ) {
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig', [
            'count_users' => $this->userRepository->count([]),
            'count_outfits' => $this->outfitRepository->count([]),
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('OutfitShare Admin')
            ->renderContentMaximized();
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::section('Usuarios');
        yield MenuItem::linkToCrud('Usuarios', 'fas fa-users', User::class);

        yield MenuItem::section('Contenido');
        yield MenuItem::linkToCrud('Outfits', 'fas fa-tshirt', Outfit::class);
        yield MenuItem::linkToCrud('Prendas', 'fas fa-tags', Prenda::class);

        yield MenuItem::section('Sitio');
        yield MenuItem::linkToRoute('Volver a la Web', 'fas fa-arrow-left', 'app_feed');
    }
}
