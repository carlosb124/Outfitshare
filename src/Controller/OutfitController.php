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

        // Manejar pre-selección desde Randomizer
        if ($ids = $request->query->get('pre_selected')) {
            $prendaIds = explode(',', $ids);
            // Necesitamos el repositorio para buscar las entidades
            // Esto es un parche rápido si no inyectamos repositorio en método, 
            // pero como tenemos EntityManager disponible:
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
        // Verificar que el usuario sea el dueño (o implementar voter más adelante para visibilidad pública)
        if ($outfit->getUser() !== $this->getUser()) {
            // Si implementamos parte social, esto cambiará. Por ahora, protección simple.
            // throw $this->createAccessDeniedException('No puedes ver este outfit.');
        }

        return $this->render('outfit/show.html.twig', [
            'outfit' => $outfit,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_outfit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Outfit $outfit, EntityManagerInterface $entityManager): Response
    {
        if ($outfit->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('No eres el dueño de este outfit');
        }

        $form = $this->createForm(OutfitType::class, $outfit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Outfit actualizado correctamente.');

            return $this->redirectToRoute('app_outfit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('outfit/edit.html.twig', [
            'outfit' => $outfit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_outfit_delete', methods: ['POST'])]
    public function delete(Request $request, Outfit $outfit, EntityManagerInterface $entityManager): Response
    {
        if ($outfit->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete' . $outfit->getId(), $request->request->get('_token'))) {
            $entityManager->remove($outfit);
            $entityManager->flush();
            $this->addFlash('success', 'Outfit eliminado.');
        }

        return $this->redirectToRoute('app_outfit_index', [], Response::HTTP_SEE_OTHER);
    }
}
