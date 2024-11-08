<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use App\Entity\User;

#[Route('/api')]
class ProfileController extends AbstractController
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    #[Route('/profile/', name: 'api_profile_trailing', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function viewProfile(): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['message' => 'Veuillez vous connecter ou créer un compte pour voir votre profil.'], Response::HTTP_UNAUTHORIZED);
        }

        // Sérialiser les données de l'utilisateur avec le groupe "user:read"
        $data = $this->serializer->serialize($user, 'json', ['groups' => 'user:read']);

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }
}
