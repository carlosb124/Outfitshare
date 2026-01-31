<?php

namespace App\EventSubscriber;

use App\Entity\Like;
use App\Entity\Outfit;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class FashionPointsSubscriber implements EventSubscriberInterface
{
    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
        ];
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        $entityManager = $args->getObjectManager();

        if ($entity instanceof Outfit) {
            // +50 Puntos al crear un Outfit
            $user = $entity->getUser();
            if ($user) {
                $user->setPuntos($user->getPuntos() + 50);
                $entityManager->flush();
            }
        }

        if ($entity instanceof Like) {
            // +10 Puntos al dueño del outfit cuando recibe un like
            $outfit = $entity->getOutfit();
            $owner = $outfit->getUser();

            // Evitar farmear puntos dándose likes a uno mismo
            if ($owner && $owner !== $entity->getUser()) {
                $owner->setPuntos($owner->getPuntos() + 10);
                $entityManager->flush();
            }
        }
    }
}
