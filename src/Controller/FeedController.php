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
    public function index(OutfitRepository $outfitRepository, \Symfony\Component\HttpFoundation\Request $request): Response
    {
        $search = $request->query->get('q');
        $category = $request->query->get('category'); // For You, Trending, etc.

        // Default to "For You" (Newest) if nothing set
        if (!$category && !$search) {
            $category = 'For You';
        }

        $outfits = $outfitRepository->findBySearchAndCategory($search, $category);

        return $this->render('feed/index.html.twig', [
            'outfits' => $outfits,
            'currentSearch' => $search,
            'currentCategory' => $category,
        ]);
    }
}
