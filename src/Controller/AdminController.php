<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\OutfitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin_dashboard')]
    public function index(UserRepository $userRepository, OutfitRepository $outfitRepository): Response
    {
        // Get total counts for the dashboard
        $countUsers = $userRepository->count([]);
        $countOutfits = $outfitRepository->count([]);
        
        return $this->render('admin/dashboard.html.twig', [
            'count_users' => $countUsers,
            'count_outfits' => $countOutfits,
        ]);
    }
}