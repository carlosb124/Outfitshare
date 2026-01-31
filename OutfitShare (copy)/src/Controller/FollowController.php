<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Service\NotificationService;

#[Route('/follow')]
#[IsGranted('ROLE_USER')]
class FollowController extends AbstractController
{
    #[Route('/{id}', name: 'app_follow_toggle', methods: ['POST'])]
    public function toggle(User $targetUser, EntityManagerInterface $entityManager, NotificationService $notificationService): JsonResponse
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if ($currentUser === $targetUser) {
            return $this->json(['error' => 'You cannot follow yourself'], 400);
        }

        $isFollowing = false;

        if ($currentUser->getFollowing()->contains($targetUser)) {
            // Unfollow
            $currentUser->removeFollowing($targetUser);
            // $targetUser->removeFollower($currentUser); // Owning side handles both usually, but let's be safe if sync needed
            $isFollowing = false;
        } else {
            // Follow
            $currentUser->addFollowing($targetUser);
            // $targetUser->addFollower($currentUser);
            $isFollowing = true;

            // Notify
            $notificationService->notifyFollow($currentUser, $targetUser);
        }

        $entityManager->flush();

        return $this->json([
            'isFollowing' => $isFollowing,
            'followersCount' => $targetUser->getFollowers()->count()
        ]);
    }
}
