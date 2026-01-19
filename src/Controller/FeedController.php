<?php

namespace App\Controller;

use App\Repository\OutfitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FeedController extends AbstractController
{
    #[Route('/', name: 'app_feed')]
    public function index(OutfitRepository $outfitRepository): Response
    {
        // Mostrar outfits más recientes de TODOS los usuarios
        $outfits = $outfitRepository->findBy([], ['fechaPublicacion' => 'DESC']);

        return $this->render('feed/index.html.twig', [
            'outfits' => $outfits,
        ]);
    }
}
