<?php   

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Symfony\Component\HttpFoundation\Request;

#[AsController]
class ErrorController
{
    public function __invoke(Request $request, FlattenException $exception, DebugLoggerInterface $logger = null): JsonResponse
    {
        return new JsonResponse([
            'message' => $exception->getMessage(),
            'data' => $exception->getTrace()
        ], $exception->getStatusCode());
    }
}
