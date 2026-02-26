<?php

namespace App\Controller;

use App\Entity\Outfit;
use App\Form\OutfitType;
use App\Repository\OutfitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/outfit')]
#[IsGranted('ROLE_USER')]
class OutfitController extends AbstractController
{
    #[Route('/', name: 'app_outfit_index', methods: ['GET'])]
    public function index(OutfitRepository $outfitRepository): Response
    {
        return $this->render('outfit/index.html.twig', [
            'outfits' => $outfitRepository->findBy(['user' => $this->getUser()], ['fechaPublicacion' => 'DESC']),
        ]);
    }

    #[Route('/new', name: 'app_outfit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $outfit = new Outfit();
        $outfit->setUser($this->getUser());
        $outfit->setFechaPublicacion(new \DateTime());

        // Preselección desde el randomizer (si viene con prendas elegidas)
        if ($ids = $request->query->get('pre_selected')) {
            $prendaIds = explode(',', $ids);
            // Buscar las prendas por ID
            
            
            foreach ($prendaIds as $id) {
                $prenda = $entityManager->getReference(\App\Entity\Prenda::class, $id);
                if ($prenda) {
                    $outfit->addPrenda($prenda);
                }
            }
        }

        $form = $this->createForm(OutfitType::class, $outfit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($outfit);
            $entityManager->flush();

            $this->addFlash('success', '¡Outfit creado con éxito! +50 Puntos de estilo.');

            return $this->redirectToRoute('app_outfit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('outfit/new.html.twig', [
            'outfit' => $outfit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_outfit_show', methods: ['GET'])]
    public function show(Outfit $outfit): Response
    {
        // Verificar permisos de visualización
        if ($outfit->getUser() !== $this->getUser()) {
            
            
        }

        return $this->render('outfit/show.html.twig', [
            'outfit' => $outfit,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_outfit_edit', methods: ['GET', 'POST'])]
    public function edit(Outfit $outfit): Response
    {
        // Redirigir a vista del outfit (no hay pantalla de edición)
        return $this->redirectToRoute('app_outfit_show', ['id' => $outfit->getId()]);
    }

    #[Route('/{id}', name: 'app_outfit_delete', methods: ['POST'])]
    public function delete(Request $request, Outfit $outfit, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if ($outfit->getUser() !== $user && !$this->isGranted('ROLE_ADMIN')) {
            if ($user instanceof \App\Entity\User && $outfit->getUser()->getId() === $user->getId()) {
                // Es el dueño del outfit
            } else {
                throw $this->createAccessDeniedException();
            }
        }

        if ($this->isCsrfTokenValid('delete' . $outfit->getId(), $request->request->get('_token'))) {
            $entityManager->remove($outfit);
            $entityManager->flush();
            $this->addFlash('success', 'Outfit eliminado.');
        }

        return $this->redirectToRoute('app_outfit_index', [], Response::HTTP_SEE_OTHER);
    }
}