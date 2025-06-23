<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class InicioController extends AbstractController
{
    #[Route('/', name: 'app_inicio',methods: ['GET'])]
    public function index(): Response
    {
        return $this->json('API Symfony 7.')
            ->setStatusCode(Response::HTTP_OK);
    }
}
