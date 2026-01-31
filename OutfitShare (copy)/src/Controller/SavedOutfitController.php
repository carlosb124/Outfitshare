<?php

namespace App\Controller;

use App\Entity\Outfit;
use App\Entity\SavedOutfit;
use App\Repository\SavedOutfitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/save')]
#[IsGranted('ROLE_USER')]
class SavedOutfitController extends AbstractController
{
    #[Route('/toggle/{id}', name: 'app_save_toggle', methods: ['POST'])]
    public function toggle(Outfit $outfit, SavedOutfitRepository $savedOutfitRepository, EntityManagerInterface $entityManager, \App\Service\NotificationService $notificationService): \Symfony\Component\HttpFoundation\JsonResponse
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $savedOutfit = $savedOutfitRepository->findOneBy(['user' => $user, 'outfit' => $outfit]);

        $isSaved = false;

        if ($savedOutfit) {
            $user->removeSavedOutfit($savedOutfit); // Update memory
            $entityManager->remove($savedOutfit);
            $isSaved = false;
        } else {
            $savedOutfit = new SavedOutfit();
            $savedOutfit->setUser($user);
            $savedOutfit->setOutfit($outfit);
            $savedOutfit->setSavedAt(new \DateTimeImmutable());
            $user->addSavedOutfit($savedOutfit); // Update memory
            $entityManager->persist($savedOutfit);
            $isSaved = true;

            // Send Notification
            $notificationService->notifySave($user, $outfit);
        }

        $entityManager->flush();

        return $this->json([
            'isActive' => $isSaved,
        ]);
    }
}
