<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Outfit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/comment')]
#[IsGranted('ROLE_USER')]
class CommentController extends AbstractController
{
    #[Route('/add/{id}', name: 'app_comment_add', methods: ['POST'])]
    public function add(Request $request, Outfit $outfit, EntityManagerInterface $entityManager, \App\Service\NotificationService $notificationService): Response
    {
        $content = $request->request->get('content');

        if (!empty($content)) {
            $comment = new Comment();
            $comment->setContent($content);
            $comment->setAuthor($this->getUser());
            $comment->setOutfit($outfit);
            $comment->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($comment);
            $entityManager->flush();

            // Send Notification
            $notificationService->notifyComment($this->getUser(), $outfit, $content);

            $this->addFlash('success', 'Comment added!');
        }

        return $this->redirectToRoute('app_outfit_show', ['id' => $outfit->getId()]);
    }
}
