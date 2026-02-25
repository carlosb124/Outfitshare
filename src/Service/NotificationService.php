<?php

namespace App\Service;

use App\Entity\Notification;
use App\Entity\Outfit;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class NotificationService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function create(User $recipient, string $type, string $message, ?User $sender = null, ?Outfit $relatedOutfit = null): void
    {
        // no notificar a los usuarios a si mismos
        if ($sender === $recipient) {
            return;
        }

        $notification = new Notification();
        $notification->setRecipient($recipient);
        $notification->setType($type);
        $notification->setMessage($message);
        $notification->setSender($sender);
        $notification->setRelatedOutfit($relatedOutfit);
        $notification->setCreatedAt(new \DateTimeImmutable());
        $notification->setIsRead(false);

        $this->entityManager->persist($notification);
        $this->entityManager->flush();
    }

    public function notifyLike(User $liker, Outfit $outfit): void
    {
        $this->create(
            recipient: $outfit->getUser(),
            type: 'like',
            message: "{$liker->getName()} liked your outfit '{$outfit->getTitulo()}'",
            sender: $liker,
            relatedOutfit: $outfit
        );
    }

    public function notifySave(User $saver, Outfit $outfit): void
    {
        $this->create(
            recipient: $outfit->getUser(),
            type: 'save',
            message: "{$saver->getName()} saved your outfit '{$outfit->getTitulo()}'",
            sender: $saver,
            relatedOutfit: $outfit
        );
    }

    public function notifyComment(User $commenter, Outfit $outfit, string $commentContent): void
    {
        // Truncate comment for the notification message
        $preview = mb_strlen($commentContent) > 30 ? mb_substr($commentContent, 0, 30) . '...' : $commentContent;

        $this->create(
            recipient: $outfit->getUser(),
            type: 'comment',
            message: "{$commenter->getName()} commented: \"{$preview}\"",
            sender: $commenter,
            relatedOutfit: $outfit
        );
    }

    public function notifyFollow(User $follower, User $followed): void
    {
        $this->create(
            recipient: $followed,
            type: 'follow',
            message: "{$follower->getName()} ha empezado a seguirte",
            sender: $follower
        );
    }

    public function notifyFollowRequest(User $requester, User $target): void
    {
        $this->create(
            recipient: $target,
            type: 'follow_request',
            message: "{$requester->getName()} quiere seguirte",
            sender: $requester
        );
    }

    public function notifyFollowAccepted(User $accepter, User $requester): void
    {
        $this->create(
            recipient: $requester,
            type: 'follow_accepted',
            message: "{$accepter->getName()} ha aceptado tu solicitud de seguimiento",
            sender: $accepter
        );
    }

    public function markAsRead(Notification $notification): void
    {
        $notification->setIsRead(true);
        $this->entityManager->flush();
    }

    public function markAllAsRead(User $user): void
    {
        foreach ($user->getNotifications() as $notification) {
            if (!$notification->isRead()) {
                $notification->setIsRead(true);
            }
        }
        $this->entityManager->flush();
    }
}