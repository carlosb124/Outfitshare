<?php

namespace App\Controller;

use App\Entity\FollowRequest;
use App\Entity\User;
use App\Repository\FollowRequestRepository;
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
    public function toggle(
        User $targetUser,
        EntityManagerInterface $entityManager,
        NotificationService $notificationService,
        FollowRequestRepository $followRequestRepository
    ): JsonResponse {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if ($currentUser === $targetUser) {
            return $this->json(['error' => 'You cannot follow yourself'], 400);
        }

        $isFollowing = $currentUser->getFollowing()->contains($targetUser);

        if ($isFollowing) {
            // Unfollow — always instant
            $currentUser->removeFollowing($targetUser);
            $entityManager->flush();

            return $this->json([
                'status' => 'unfollowed',
                'isFollowing' => false,
                'isPending' => false,
                'followersCount' => $targetUser->getFollowers()->count(),
            ]);
        }

        // Check if target profile is private
        if (!$targetUser->isPublic()) {
            // Check for existing request
            $existingRequest = $followRequestRepository->findBetweenUsers($currentUser, $targetUser);

            if ($existingRequest) {
                if ($existingRequest->isPending()) {
                    // Cancel the pending request
                    $entityManager->remove($existingRequest);
                    $entityManager->flush();

                    return $this->json([
                        'status' => 'request_cancelled',
                        'isFollowing' => false,
                        'isPending' => false,
                        'followersCount' => $targetUser->getFollowers()->count(),
                    ]);
                }
                // If previously rejected, create a new request
                $entityManager->remove($existingRequest);
                $entityManager->flush();
            }

            // Create a follow request
            $followRequest = new FollowRequest();
            $followRequest->setRequester($currentUser);
            $followRequest->setTarget($targetUser);
            $entityManager->persist($followRequest);
            $entityManager->flush();

            // Notify target user
            $notificationService->notifyFollowRequest($currentUser, $targetUser);

            return $this->json([
                'status' => 'request_sent',
                'isFollowing' => false,
                'isPending' => true,
                'followersCount' => $targetUser->getFollowers()->count(),
            ]);
        }

        // Public profile — instant follow
        $currentUser->addFollowing($targetUser);
        $entityManager->flush();

        $notificationService->notifyFollow($currentUser, $targetUser);

        return $this->json([
            'status' => 'following',
            'isFollowing' => true,
            'isPending' => false,
            'followersCount' => $targetUser->getFollowers()->count(),
        ]);
    }

    #[Route('/request/{id}/accept', name: 'app_follow_request_accept', methods: ['POST'])]
    public function acceptRequest(
        FollowRequest $followRequest,
        EntityManagerInterface $entityManager,
        NotificationService $notificationService
    ): JsonResponse {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        // Only the target user can accept
        if ($followRequest->getTarget() !== $currentUser) {
            return $this->json(['error' => 'Unauthorized'], 403);
        }

        if (!$followRequest->isPending()) {
            return $this->json(['error' => 'Request already processed'], 400);
        }

        // Accept: add the follow relationship
        $requester = $followRequest->getRequester();
        $requester->addFollowing($currentUser);
        $followRequest->setStatus(FollowRequest::STATUS_ACCEPTED);

        $entityManager->flush();

        // Notify requester that their request was accepted
        $notificationService->notifyFollowAccepted($currentUser, $requester);

        return $this->json([
            'status' => 'accepted',
            'followersCount' => $currentUser->getFollowers()->count(),
        ]);
    }

    #[Route('/request/{id}/reject', name: 'app_follow_request_reject', methods: ['POST'])]
    public function rejectRequest(
        FollowRequest $followRequest,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        // Only the target user can reject
        if ($followRequest->getTarget() !== $currentUser) {
            return $this->json(['error' => 'Unauthorized'], 403);
        }

        if (!$followRequest->isPending()) {
            return $this->json(['error' => 'Request already processed'], 400);
        }

        $followRequest->setStatus(FollowRequest::STATUS_REJECTED);
        $entityManager->flush();

        return $this->json([
            'status' => 'rejected',
        ]);
    }
}