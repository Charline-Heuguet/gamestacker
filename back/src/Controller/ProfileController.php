<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\User;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api')]
class ProfileController extends AbstractController
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    #[Route('/profile', name: 'api_profile', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function viewProfile(): JsonResponse
    {
        $user = $this->getUser();

        // Vérifie si l'utilisateur est bien une instance de User
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User not authenticated or invalid user type'], 401);
        }

        // Utilisation d'une URL de base statique pour l'image
        $imageUrl = $user->getImage() ? 'http://localhost:8000/bundles/images/users/' . $user->getImage() : null;

        // Données de l'utilisateur avec l'URL complète de l'image
        $data = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'pseudo' => $user->getPseudo(),
            'description' => $user->getDescription(),
            'age' => $user->getAge(),
            'discord' => $user->getDiscord(),
            'gender' => $user->getGender(),
            'image' => $imageUrl,
        ];

        return new JsonResponse($data, 200);
    }
}
