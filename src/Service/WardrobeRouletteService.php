<?php

namespace App\Service;

use App\Entity\Outfit;
use App\Entity\User;
use App\Enum\SeasonEnum;
use App\Repository\PrendaRepository;
use Doctrine\Common\Collections\ArrayCollection;

class WardrobeRouletteService
{
    public function __construct(
        private PrendaRepository $prendaRepository
    ) {
    }

    public function spin(User $user, ?SeasonEnum $season = null): Outfit
    {
        // Obtener prendas disponibles
        $criteria = ['user' => $user];
        if ($season && $season !== SeasonEnum::ALL_SEASON) {
            $criteria['season'] = $season;
        }

        $allClothes = $this->prendaRepository->findBy($criteria);

        if (empty($allClothes)) {
            // Si no hay resultados por temporada, buscar sin filtro
            if ($season) {
                $allClothes = $this->prendaRepository->findBy(['user' => $user]);
            }

            if (empty($allClothes)) {
                throw new \RuntimeException('Not enough clothes to generate an outfit');
            }
        }

        // Agrupar por categoría
        
        $tops = $this->filterByCategory($allClothes, ['top', 'shirt', 't-shirt', 'blouse', 'sweater']);
        $bottoms = $this->filterByCategory($allClothes, ['pants', 'jeans', 'skirt', 'shorts']);
        $shoes = $this->filterByCategory($allClothes, ['shoes', 'sneakers', 'boots', 'sandals']);
        $dresses = $this->filterByCategory($allClothes, ['dress']);

        $draftOutfit = new Outfit();
        $draftOutfit->setUser($user);
        $draftOutfit->setTitulo('Random Outfit ' . date('d/m/Y'));
        $draftOutfit->setFechaPublicacion(new \DateTime());
        $draftOutfit->setType(\App\Enum\OutfitTypeEnum::USER_GENERATED);

        // 3. Logic: Dress OR (Top + Bottom)
        $useDress = !empty($dresses) && (empty($tops) || empty($bottoms) || rand(0, 100) < 30);

        if ($useDress) {
            $selectedDress = $dresses[array_rand($dresses)];
            $draftOutfit->addPrenda($selectedDress);
        } else {
            if (!empty($tops)) {
                $selectedTop = $tops[array_rand($tops)];
                $draftOutfit->addPrenda($selectedTop);
            }
            if (!empty($bottoms)) {
                $selectedBottom = $bottoms[array_rand($bottoms)];
                $draftOutfit->addPrenda($selectedBottom);
            }
        }

        // Añadir calzado si hay
        if (!empty($shoes)) {
            $selectedShoes = $shoes[array_rand($shoes)];
            $draftOutfit->addPrenda($selectedShoes);
        }

        return $draftOutfit;
    }

    private function filterByCategory(array $items, array $keywords): array
    {
        return array_filter($items, function ($item) use ($keywords) {
            // Categoría como string 
            // Better to use an Enum for Category in the future.
            $category = strtolower($item->getCategoria() ?? '');
            return in_array($category, $keywords) || $this->matchesKeyword($category, $keywords);
        });
    }

    private function matchesKeyword(string $category, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if (str_contains($category, $keyword)) {
                return true;
            }
        }
        return false;
    }
}
