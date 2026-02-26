<?php

namespace App\Controller;

use App\Entity\Prenda;
use App\Form\PrendaType;
use App\Repository\PrendaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/prenda')]
class PrendaController extends AbstractController
{
    #[Route('/', name: 'app_prenda_index', methods: ['GET'])]
    public function index(PrendaRepository $prendaRepository): Response
    {
        $prendas = $prendaRepository->findBy(['user' => $this->getUser()]);

        return $this->render('prenda/index.html.twig', [
            'prendas' => $prendas,
        ]);
    }

    #[Route('/new', name: 'app_prenda_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, \App\Service\CloudinaryService $cloudinaryService): Response
    {
        $prenda = new Prenda();
        $prenda->setUser($this->getUser());

        $form = $this->createForm(PrendaType::class, $prenda);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imagenFile = $form->get('imagenFile')->getData();

            if ($imagenFile) {
                try {
                    $secureUrl = $cloudinaryService->uploadImage($imagenFile, 'outfitshare/prendas');
                    $prenda->setImagen($secureUrl);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Error al subir la imagen a Cloudinary: ' . $e->getMessage());
                }
            }

            $entityManager->persist($prenda);
            $entityManager->flush();

            $this->addFlash('success', '¡Prenda añadida correctamente!');
            return $this->redirectToRoute('app_prenda_index');
        }

        return $this->render('prenda/new.html.twig', [
            'prenda' => $prenda,
            'form' => $form,
        ]);
    }

    #[Route('/ajax-demo', name: 'app_prenda_ajax_demo', methods: ['GET'])]
    public function ajaxDemo(): Response
    {
        return $this->render('prenda/ajax.html.twig');
    }

    #[Route('/fetch-demo', name: 'app_prenda_fetch_demo', methods: ['GET'])]
    public function fetchDemo(): Response
    {
        return $this->render('prenda/fetch.html.twig');
    }
    #[Route('/{id}/edit', name: 'app_prenda_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Prenda $prenda, EntityManagerInterface $entityManager, \App\Service\CloudinaryService $cloudinaryService): Response
    {
        if ($prenda->getUser() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('No puedes editar esta prenda.');
        }

        $form = $this->createForm(PrendaType::class, $prenda);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imagenFile = $form->get('imagenFile')->getData();

            if ($imagenFile) {
                try {
                    $secureUrl = $cloudinaryService->uploadImage($imagenFile, 'outfitshare/prendas');
                    $prenda->setImagen($secureUrl);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Error al subir la imagen a Cloudinary: ' . $e->getMessage());
                }
            }

            $entityManager->flush();

            $this->addFlash('success', '¡Prenda actualizada!');
            return $this->redirectToRoute('app_prenda_index');
        }

        return $this->render('prenda/edit.html.twig', [
            'prenda' => $prenda,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_prenda_delete', methods: ['GET', 'POST'])]
    public function delete(Request $request, Prenda $prenda, EntityManagerInterface $entityManager): Response
    {
        if ($prenda->getUser() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('No puedes eliminar esta prenda.');
        }

        // En una app real, verificaríamos el token CSRF aquí.
        // if ($this->isCsrfTokenValid('delete'.$prenda->getId(), $request->request->get('_token'))) { ... }

        $entityManager->remove($prenda);
        $entityManager->flush();

        $this->addFlash('success', 'Prenda eliminada correctamente.');
        return $this->redirectToRoute('app_profile_show', ['id' => $this->getUser()->getId()]);
    }
}