<?php

namespace App\Controller\Api;

use App\Entity\Prenda;
use App\Repository\PrendaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/api/prendas')]
class ApiPrendaController extends AbstractController
{
    /**
     * GET /api/prendas - Listar todas las prendas del usuario autenticado
     */
    #[Route('', name: 'api_prenda_index', methods: ['GET'])]
    public function index(PrendaRepository $prendaRepository): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->json([
                'success' => false,
                'error' => 'Usuario no autenticado'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $prendas = $prendaRepository->findBy(['user' => $user]);

        $data = array_map(function (Prenda $prenda) {
            return $this->serializePrenda($prenda);
        }, $prendas);

        return $this->json([
            'success' => true,
            'data' => $data,
            'count' => count($data)
        ]);
    }

    /**
     * GET /api/prendas/{id} - Obtener una prenda específica
     */
    #[Route('/{id}', name: 'api_prenda_show', methods: ['GET'])]
    public function show(Prenda $prenda): JsonResponse
    {
        // Verificar que la prenda pertenece al usuario
        if ($prenda->getUser() !== $this->getUser()) {
            return $this->json([
                'success' => false,
                'error' => 'No tienes permiso para ver esta prenda'
            ], Response::HTTP_FORBIDDEN);
        }

        return $this->json([
            'success' => true,
            'data' => $this->serializePrenda($prenda)
        ]);
    }

    /**
     * POST /api/prendas - Crear una nueva prenda
     */
    #[Route('', name: 'api_prenda_create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): JsonResponse {
        $user = $this->getUser();

        if (!$user) {
            return $this->json([
                'success' => false,
                'error' => 'Usuario no autenticado'
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Obtener datos del request (soporta JSON y form-data)
        $contentType = $request->headers->get('Content-Type');

        if (str_contains($contentType, 'application/json')) {
            $data = json_decode($request->getContent(), true);
        } else {
            $data = $request->request->all();
        }

        // Validar campos requeridos
        if (empty($data['nombre']) || empty($data['categoria'])) {
            return $this->json([
                'success' => false,
                'error' => 'Los campos nombre y categoria son obligatorios'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Crear la prenda
        $prenda = new Prenda();
        $prenda->setNombre($data['nombre']);
        $prenda->setCategoria($data['categoria']);
        $prenda->setMarca($data['marca'] ?? null);
        $prenda->setUser($user);

        // Manejar imagen si se envía
        $imagenFile = $request->files->get('imagen');
        if ($imagenFile) {
            $originalFilename = pathinfo($imagenFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $imagenFile->guessExtension();

            try {
                $imagenFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );
                $prenda->setImagen($newFilename);
            } catch (\Exception $e) {
                return $this->json([
                    'success' => false,
                    'error' => 'Error al subir la imagen: ' . $e->getMessage()
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        $entityManager->persist($prenda);
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Prenda creada correctamente',
            'data' => $this->serializePrenda($prenda)
        ], Response::HTTP_CREATED);
    }

    /**
     * PUT /api/prendas/{id} - Actualizar una prenda
     */
    #[Route('/{id}', name: 'api_prenda_update', methods: ['PUT', 'PATCH'])]
    public function update(
        Request $request,
        Prenda $prenda,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        // Verificar permisos
        if ($prenda->getUser() !== $this->getUser()) {
            return $this->json([
                'success' => false,
                'error' => 'No tienes permiso para editar esta prenda'
            ], Response::HTTP_FORBIDDEN);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['nombre'])) {
            $prenda->setNombre($data['nombre']);
        }
        if (isset($data['categoria'])) {
            $prenda->setCategoria($data['categoria']);
        }
        if (array_key_exists('marca', $data)) {
            $prenda->setMarca($data['marca']);
        }

        $entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Prenda actualizada correctamente',
            'data' => $this->serializePrenda($prenda)
        ]);
    }

    /**
     * DELETE /api/prendas/{id} - Eliminar una prenda
     */
    #[Route('/{id}', name: 'api_prenda_delete', methods: ['DELETE'])]
    public function delete(Prenda $prenda, EntityManagerInterface $entityManager): JsonResponse
    {
        // Verificar permisos
        if ($prenda->getUser() !== $this->getUser()) {
            return $this->json([
                'success' => false,
                'error' => 'No tienes permiso para eliminar esta prenda'
            ], Response::HTTP_FORBIDDEN);
        }

        // Eliminar imagen si existe
        if ($prenda->getImagen()) {
            $imagePath = $this->getParameter('images_directory') . '/' . $prenda->getImagen();
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $entityManager->remove($prenda);
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Prenda eliminada correctamente'
        ]);
    }

    /**
     * GET /api/prendas/categoria/{categoria} - Filtrar prendas por categoría
     */
    #[Route('/categoria/{categoria}', name: 'api_prenda_by_categoria', methods: ['GET'], priority: 10)]
    public function byCategoria(string $categoria, PrendaRepository $prendaRepository): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->json([
                'success' => false,
                'error' => 'Usuario no autenticado'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $prendas = $prendaRepository->findBy([
            'user' => $user,
            'categoria' => $categoria
        ]);

        $data = array_map(function (Prenda $prenda) {
            return $this->serializePrenda($prenda);
        }, $prendas);

        return $this->json([
            'success' => true,
            'data' => $data,
            'count' => count($data),
            'categoria' => $categoria
        ]);
    }

    /**
     * GET /api/prendas/categorias/list - Obtener lista de categorías disponibles
     */
    #[Route('/categorias/list', name: 'api_prenda_categorias', methods: ['GET'], priority: 20)]
    public function getCategorias(PrendaRepository $prendaRepository): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->json([
                'success' => false,
                'error' => 'Usuario no autenticado'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $prendas = $prendaRepository->findBy(['user' => $user]);
        $categorias = array_unique(array_map(fn($p) => $p->getCategoria(), $prendas));

        return $this->json([
            'success' => true,
            'data' => array_values($categorias)
        ]);
    }

    /**
     * Serializar una prenda a array
     */
    private function serializePrenda(Prenda $prenda): array
    {
        return [
            'id' => $prenda->getId(),
            'nombre' => $prenda->getNombre(),
            'marca' => $prenda->getMarca(),
            'categoria' => $prenda->getCategoria(),
            'imagen' => $prenda->getImagen()
                ? '/uploads/images/' . $prenda->getImagen()
                : null
        ];
    }
}
