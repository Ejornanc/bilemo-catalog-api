<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LoginController extends AbstractController
{
    #[Route('/api/login', name: 'api_login_info', methods: ['GET'])]
    public function loginInfo(): JsonResponse
    {
        return $this->json([
            'message' => 'JWT login endpoint. Use POST /api/login with {"email", "password"} to obtain a token.',
            'method' => 'POST',
            'path' => '/api/login',
            'body_example' => [
                'email' => 'partner@example.com',
                'password' => 'password123'
            ]
        ], Response::HTTP_METHOD_NOT_ALLOWED);
    }
}
