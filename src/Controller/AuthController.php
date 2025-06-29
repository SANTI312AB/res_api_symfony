<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AuthForm;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;
use Nelmio\ApiDocBundle\Attributes\Security;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class AuthController extends AbstractController
{
    private $entityManager;
    
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/login', name: 'app_auth', methods:['POST'])]
    #[OA\Tag(name: 'Auth')]
    #[OA\RequestBody(
        description: 'Login de usuario',
        content: new Model(type: AuthForm::class)
    )]
    public function login(): Response
    {
        return $this->json([
            'message' => 'Login successful'
        ]);
    }



    #[Route('/register', name: 'app_register', methods: ['POST'])]
    #[OA\Tag(name: 'Auth')]
    #[OA\RequestBody(
        description: 'Registro de usuario',
        content: new Model(type: AuthForm::class)
    )]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Obtener datos del formulario (ejemplo)
        $user = new User();
        $form = $this->createForm(AuthForm::class, $user);
        $form->handleRequest($request);

         // Asegúrate de que User es tu entidad de usuario

        if ($form->isSubmitted() && $form->isValid()) {

            $password = $form->get('password')->getData();

            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $password
            );
            $user->setPassword($hashedPassword);

            // Guardar el usuario (ejemplo)

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->json([
                'message' => 'Usuario registrado exitosamente',
            ])->setStatusCode(Response::HTTP_CREATED);
        }

        $errors = [];
        foreach ($form->getErrors(true, true) as $error) {
            $propertyName = $error->getOrigin()->getName();
            $errors[$propertyName][] = $error->getMessage();
        }


        return $this->json([
            'message' => 'Algunos campos no son válidos, por favor verifique los datos e intente nuevamente.',
            'errors' => $errors
        ])->setStatusCode(Response::HTTP_BAD_REQUEST);

    }

    #[Route('/api/user/edit', name: 'app_user_edit', methods: ['PATCH'])]
    #[OA\Tag(name: 'Auth')]
    #[OA\RequestBody(
        description: 'Editar usuario',
        content: new Model(type: AuthForm::class)
    )]
    #[Security(name: 'Bearer')]
    public function editUser(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        UserRepository $userRepository
    ): Response {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->json(['message' => 'Usuario no encontrado.'], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(AuthForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('password')->getData();

            if ($password) {
                $hashedPassword = $passwordHasher->hashPassword($user, $password);
                $userRepository->upgradePassword($user, $hashedPassword);
                // upgradePassword ya hace flush()
            } 
            
            $this->entityManager->flush();

            return $this->json(['message' => 'Usuario actualizado exitosamente']);
        }

        $errors = [];
        foreach ($form->getErrors(true, true) as $error) {
            $propertyName = $error->getOrigin()->getName();
            $errors[$propertyName][] = $error->getMessage();
        }

        return $this->json([
            'message' => 'Algunos campos no son válidos, por favor verifique los datos e intente nuevamente.',
            'errors' => $errors
        ], Response::HTTP_BAD_REQUEST);
    }


    #[Route('/api/user', name: 'app_user', methods: ['GET'])]
    #[OA\Tag(name: 'Auth')]
    #[Security(name: 'Bearer')]
    public function index(): Response
    {
        $user = $this->getUser(); // Obtener el usuario autenticado

        if (!$user instanceof User) {
            return $this->json([
                'message' => 'Usuario no encontrado.'
            ], Response::HTTP_NOT_FOUND);
        }

        return $this->json($user)->setStatusCode(Response::HTTP_OK);
    }


    #[Route('/api/logout', name: 'app_logout', methods: ['POST'])]
    #[OA\Tag(name: 'Auth')]
    #[Security(name: 'Bearer')]
    public function logout(
        Request $request,
        EventDispatcherInterface $eventDispatcher,
        TokenStorageInterface $tokenStorage
    ): Response {
        // Se dispara el evento que LexikJWTAuthenticationBundle escucha
        $eventDispatcher->dispatch(new LogoutEvent($request, $tokenStorage->getToken()));

        return $this->json([
            'message' => 'Logout exitoso'
        ])->setStatusCode(Response::HTTP_OK);
    }
}

