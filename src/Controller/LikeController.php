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
    public function toggle(Outfit $outfit, LikeRepository $likeRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        // Buscar si ya existe el like
        $like = $likeRepository->findOneByUserAndOutfit($user, $outfit);

        if ($like) {
            // Si existe, lo quitamos
            $entityManager->remove($like);
            $entityManager->flush();
            $isLiked = false;
        } else {
            // Si no existe, lo creamos
            $like = new Like();
            $like->setUser($user);
            $like->setOutfit($outfit);
            $entityManager->persist($like);
            $entityManager->flush();
            $isLiked = true;
        }

        // Devolver la vista parcial del botón para Turbo Frame o HTML puro
        return $this->render('like/_button.html.twig', [
            'outfit' => $outfit,
            'isLiked' => $isLiked,
        ]);
    }
}
