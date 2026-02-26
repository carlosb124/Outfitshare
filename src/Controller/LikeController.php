<?php

namespace App\Controller;

use App\Entity\Like;
use App\Entity\Outfit;
use App\Repository\LikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/like')]
#[IsGranted('ROLE_USER')]
class LikeController extends AbstractController
{
    #[Route('/toggle/{id}', name: 'app_like_toggle', methods: ['POST'])]
    public function toggle(Outfit $outfit, LikeRepository $likeRepository, EntityManagerInterface $entityManager, \App\Service\NotificationService $notificationService): \Symfony\Component\HttpFoundation\JsonResponse
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        // Comprobar si el usuario ya dio like
        $like = $likeRepository->findOneByUserAndOutfit($user, $outfit);

        if ($like) {
            // Ya existe → quitar like
            $outfit->removeLike($like); // Actualizar colección en memoria
            $entityManager->remove($like);
            $entityManager->flush();
            $isLiked = false;
        } else {
            // No existe → dar like
            $like = new Like();
            $like->setUser($user);
            $like->setOutfit($outfit);
            $outfit->addLike($like); // Actualizar colección en memoria
            $entityManager->persist($like);
            $entityManager->flush();
            $isLiked = true;

            // Send Notification
            $notificationService->notifyLike($user, $outfit);
        }

        return $this->json([
            'isActive' => $isLiked,
            'count' => $outfit->getLikes()->count()
        ]);
    }
}
