<?php

namespace App\Controller;

use App\Entity\Productos;
use App\Entity\User;
use App\Form\ProductosForm;
use App\Repository\ProductosRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;
use Nelmio\ApiDocBundle\Attributes\Security;

#[OA\Tag(name: 'Productos')]
final class ProductosController extends AbstractController
{
    #[Route('/productos', name: 'app_productos_index', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Productos::class))
        ),
        description: 'Lista de post publicos.'
    )]
    public function index(ProductosRepository $productosRepository): Response
    {
        $productos = $productosRepository->findAll();
       /* return $this->json(
            array_map(fn(Productos $producto) => ['nombre' => $producto->getNombre()], $productos)
        )->setStatusCode(Response::HTTP_OK);*/

        return $this->json($productos)->setStatusCode(Response::HTTP_OK);
    }


    #[Route('/api/productos', name: 'app_productos_list', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Productos::class))
        ),
        description: 'Lista de mis productos.'
    )]
    #[Security(name: 'Bearer')]
    public function list(ProductosRepository $productosRepository): Response
    {
        $user= $this->getUser();
        $productos = $productosRepository->findBy(['user' => $user]);
        return $this->json($productos)->setStatusCode(Response::HTTP_OK);
    }


    #[Route('/api/productos/{id}', name: 'app_productos_show', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Mostrar un producto',
        content: new Model(type: Productos::class)
    )]
    #[Security(name: 'Bearer')]
    public function private_show(?Productos $producto): Response
    {
        $user = $this->getUser();
        if (!$producto) {
            return $this->json(
                ['message' => 'Producto not found']
            )->setStatusCode(Response::HTTP_NOT_FOUND);
        }
        if ($producto->getUser() !== $user) {
            return $this->json(
                ['message' => 'No tienes permiso para ver este producto']
            )->setStatusCode(Response::HTTP_FORBIDDEN);
        }

        return $this->json($producto)->setStatusCode(Response::HTTP_OK);
    }

    #[Route('/api/productos/new', name: 'app_productos_new', methods: ['POST'])]
    #[OA\RequestBody(
        description: 'Añadir nuevo post',
        content: new Model(type: ProductosForm::class)
    )]
    #[Security(name: 'Bearer')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $producto = new Productos();
        $form = $this->createForm(ProductosForm::class, $producto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $producto->setUser($user);
            $entityManager->persist($producto);
            $entityManager->flush();

            return $this->json(
                [ 'message' => 'Producto created successfully', 'id' => $producto->getId()]
            )->setStatusCode(Response::HTTP_OK);
        }
        
        $errors = [];
        foreach ($form->getErrors(true, true) as $error) {
            $propertyName = $error->getOrigin()->getName();
            $errors[$propertyName][] = $error->getMessage();
        }
        return $this->json(['message'=>'Algunos campos no son válidos, por favor verifique los datos e intente nuevamente.',
              'errors'=>$errors
        ])->setStatusCode(Response::HTTP_BAD_REQUEST);
    }

    
    #[Route('/productos/{id}', name: 'app_productos_show', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Mostrar un producto',
        content: new Model(type: Productos::class)
    )]
    public function publci_show(?Productos $producto): Response
    {
        if (!$producto) {
            return $this->json(
                ['message' => 'Producto not found']
            )->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        return $this->json($producto)->setStatusCode(Response::HTTP_OK);

    }

    #[Route('/api/productos/{id}/edit', name: 'app_productos_edit', methods: [ 'PATCH'])]
    #[OA\RequestBody(
        description: 'Añadir nuevo post',
        content: new Model(type: ProductosForm::class)
    )]
    #[Security(name: 'Bearer')]
    public function edit(Request $request, ?Productos $producto, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$producto) {
            return $this->json(
                ['message' => 'Producto not found']
            )->setStatusCode(Response::HTTP_NOT_FOUND);
        }
        if ($producto->getUser() !== $user) {
            return $this->json(
                ['message' => 'No tienes permiso para editar este producto']
            )->setStatusCode(Response::HTTP_FORBIDDEN);
        }


        $form = $this->createForm(ProductosForm::class, $producto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->json(
                ['message' => 'Producto updated successfully', 'id' => $producto->getId()]
            )->setStatusCode(Response::HTTP_OK);
        }

        $errors = [];
        foreach ($form->getErrors(true, true) as $error) {
            $propertyName = $error->getOrigin()->getName();
            $errors[$propertyName][] = $error->getMessage();
        }
        return $this->json(['message'=>'Algunos campos no son válidos, por favor verifique los datos e intente nuevamente.',
              'errors'=>$errors,
              'id' => $producto->getId()
        ])->setStatusCode(Response::HTTP_BAD_REQUEST);
    }


    #[Route('/api/productos/{id}', name: 'app_productos_delete', methods: ['DELETE'])]
    #[OA\Response(
        response: 200,
        description: 'Eliminar un producto',
        content: new Model(type: Productos::class)
    )]
    #[Security(name: 'Bearer')]
    public function delete(?Productos $producto, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$producto) {
            return $this->json(
                ['message' => 'Producto not found']
            )->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        if ($producto->getUser() !== $user) {
            return $this->json(
                ['message' => 'No tienes permiso para eliminar este producto']
            )->setStatusCode(Response::HTTP_FORBIDDEN);
        }
        
        $entityManager->remove($producto);
        $entityManager->flush();

        return $this->json(
            ['message' => 'Producto deleted successfully']
        )->setStatusCode(Response::HTTP_OK);     
    }
}
