<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\PrendaRepository;

class StatsService
{
    public function __construct(
        private PrendaRepository $prendaRepository
    ) {
    }

    public function calculateWardrobeValue(User $user): float
    {
        $items = $this->prendaRepository->findBy(['user' => $user]);
        $total = 0.0;
        foreach ($items as $item) {
            $total += $item->getPrice() ?? 0.0;
        }
        return $total;
    }

    public function getCategoryDistribution(User $user): array
    {
        // This should theoretically use a text-based query or GroupBy in Repository
        // Doing in-memory for MVP
        $items = $this->prendaRepository->findBy(['user' => $user]);
        $stats = [];
        foreach ($items as $item) {
            $cat = $item->getCategoria() ?? 'Uncategorized';
            if (!isset($stats[$cat])) {
                $stats[$cat] = 0;
            }
            $stats[$cat]++;
        }
        return $stats;
    }
}
