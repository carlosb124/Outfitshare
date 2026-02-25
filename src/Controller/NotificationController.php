<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Service\NotificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/notifications')]
#[IsGranted('ROLE_USER')]
class NotificationController extends AbstractController
{
    #[Route('/unread-count', name: 'app_notification_unread_count', methods: ['GET'])]
    public function unreadCount(\App\Repository\NotificationRepository $notificationRepository): Response
    {
        $count = $notificationRepository->countUnread($this->getUser());

        return $this->json(['count' => $count]);
    }

    #[Route('/test', name: 'app_notification_test')]
    public function test(\App\Service\NotificationService $notificationService, \Doctrine\ORM\EntityManagerInterface $em): Response
    {
        //Alerta autogenerada para hacer tests
        $user = $this->getUser();

        $n = new \App\Entity\Notification();
        $n->setRecipient($user);
        $n->setType('system');
        $n->setMessage("Esto es para generar el punto rojo 🔴");
        $n->setIsRead(false);
        $n->setCreatedAt(new \DateTimeImmutable());

        $em->persist($n);
        $em->flush();

        return $this->redirectToRoute('app_feed');
    }

    #[Route('/', name: 'app_notification_index')]
    public function index(NotificationService $notificationService, \App\Repository\NotificationRepository $notificationRepository, \App\Repository\FollowRequestRepository $followRequestRepository): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $notificationService->markAllAsRead($user);

        $notifications = $notificationRepository->findBy(
            ['recipient' => $user],
            ['createdAt' => 'DESC']
        );

        return $this->render('notification/index.html.twig', [
            'notifications' => $notifications,
            'pendingRequests' => $followRequestRepository->findPendingForUser($user),
        ]);
    }
}