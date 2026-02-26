<?php

namespace App\Controller;

use App\Repository\PrendaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/randomizer')]
#[IsGranted('ROLE_USER')]
class RandomizerController extends AbstractController
{
    #[Route('/', name: 'app_randomizer_index')]
    public function index(PrendaRepository $prendaRepository, Request $request): Response
    {
        $user = $this->getUser();

        // Obtener las prendas del usuario actual
        $prendas = $prendaRepository->findBy(['user' => $user]);

        
        
        

        $tops = [];
        $bottoms = [];
        $shoes = [];

        foreach ($prendas as $prenda) {
            $cat = mb_strtolower($prenda->getCategoria());
            if (str_contains($cat, 'superior') || str_contains($cat, 'camisa') || str_contains($cat, 'camiseta') || str_contains($cat, 'chaqueta') || str_contains($cat, 'top')) {
                $tops[] = $prenda;
            } elseif (str_contains($cat, 'inferior') || str_contains($cat, 'pantalon') || str_contains($cat, 'falla') || str_contains($cat, 'jeans')) {
                $bottoms[] = $prenda;
            } elseif (str_contains($cat, 'calzado') || str_contains($cat, 'zapatos') || str_contains($cat, 'zapatillas')) {
                $shoes[] = $prenda;
            }
        }

        $randomOutfit = [
            'top' => !empty($tops) ? $tops[array_rand($tops)] : null,
            'bottom' => !empty($bottoms) ? $bottoms[array_rand($bottoms)] : null,
            'shoes' => !empty($shoes) ? $shoes[array_rand($shoes)] : null,
        ];

        // Mínimo 2 prendas para proponer un outfit
        $hasEnough = ($randomOutfit['top'] && $randomOutfit['bottom']);

        return $this->render('randomizer/index.html.twig', [
            'outfit' => $randomOutfit,
            'hasEnough' => $hasEnough,
        ]);
    }
}
