<?php

namespace App\Controller;

use App\Repository\OutfitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class FeedController extends AbstractController
{
    #[Route('/feed', name: 'app_feed')]
    #[IsGranted('ROLE_USER')]
    public function index(OutfitRepository $outfitRepository, \App\Repository\UserRepository $userRepository, \Symfony\Component\HttpFoundation\Request $request): Response
    {
        $search = $request->query->get('q');
        $category = $request->query->get('category'); // filtro de categoría

        // Por defecto mostrar "Para Ti" (más recientes)
        if (!$category && !$search) {
            $category = 'Para Ti';
        }

        $outfits = $outfitRepository->findBySearchAndCategory($search, $category, $this->getUser());
        
        $users = [];
        if ($search) {
            // Buscar perfiles (excluir al propio usuario)
            $users = $userRepository->searchProfiles($search, $this->getUser() ? $this->getUser()->getId() : null);
        }

        return $this->render('feed/index.html.twig', [
            'outfits' => $outfits,
            'users' => $users,
            'currentSearch' => $search,
            'currentCategory' => $category,
        ]);
    }
}