<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/api', name: 'api_')]
class UserController extends AbstractController
{
    #[Route('/signup', name: 'signup', methods: ['POST'])]
    public function signup(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        UserRepository $userRepository
    ): JsonResponse {
        // Étape 1 : Récupérer les données depuis la requête
        $data = json_decode($request->getContent(), true);

        // Étape 2 : Vérifier si l'email est déjà utilisé
        $existingUser = $userRepository->findOneBy(['email' => $data['email'] ?? '']);
        if ($existingUser) {
            return new JsonResponse(
                ['error' => "L'adresse email est déjà utilisée."],
                JsonResponse::HTTP_CONFLICT
            );
        }

        // Étape 3 : Créer un nouvel utilisateur et le remplir
        $user = new User();
        $user->setEmail($data['email'] ?? '');
        $user->setPseudo($data['pseudo'] ?? '');
        $user->setAge($data['age'] ?? null);
        $user->setGender($data['gender'] ?? null);
        $user->setDiscord($data['discord'] ?? null);
        $user->setDescription($data['description'] ?? null);
        $user->setPassword($data['password'] ?? null);
        $user->setRoles(['ROLE_USER']);

        // Étape 4 : Valider l'entité User
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }

            return new JsonResponse(['errors' => $errorMessages], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Étape 5 : Hasher le mot de passe
        if (isset($data['password'])) {
            $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
            $user->setPassword($hashedPassword);
        } else {
            return new JsonResponse(['error' => "Le mot de passe est requis"], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Étape 6 : Sauvegarder l'utilisateur en BDD
        $entityManager->persist($user);
        $entityManager->flush();

        // Étape 7 : Retourner une réponse avec les informations de l'utilisateur
        return new JsonResponse([
            'message' => 'Utilisateur inscrit avec succès',
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'pseudo' => $user->getPseudo(),
                'age' => $user->getAge(),
                'gender' => $user->getGender(),
                'discord' => $user->getDiscord(),
                'description' => $user->getDescription(),
            ]
        ], JsonResponse::HTTP_CREATED);
    }
    #[Route('/profile/comments', name: 'profile_comments', methods: ['GET'])]
    public function getCommentedArticlesAndForumsWithTitles(UserRepository $userRepository): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Utilisateur non authentifié'], 401);
        }

        $comments = $userRepository->findCommentedArticlesAndForumsWithTitles($user->getId());

        return $this->json($comments);
    }

    #[Route('/profile/announcements', name: 'profile_announcements', methods: ['GET'])]
    public function getUserAnnouncements(UserRepository $userRepository): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Utilisateur non authentifié'], 401);
        }

        $announcements = $userRepository->findUserAnnouncements($user->getId());

        return $this->json($announcements);
    }


    #[Route('/profile/update', name: 'profile_update', methods: ['PUT'])]
    public function updateProfile(
        Request $request,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        // Récupérer l'utilisateur actuel
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Utilisateur non authentifié'], 401);
        }

        // Décoder les données JSON de la requête
        $data = json_decode($request->getContent(), true);

        // Mettre à jour les informations du profil
        if (isset($data['pseudo'])) {
            $user->setPseudo($data['pseudo']);
        }
        if (isset($data['description'])) {
            $user->setDescription($data['description']);
        }
        if (isset($data['age'])) {
            $user->setAge($data['age']);
        }
        if (isset($data['gender'])) {
            $user->setGender($data['gender']);
        }
        if (isset($data['discord'])) {
            $user->setDiscord($data['discord']);
        }

        // Gestion du mot de passe : uniquement s'il est fourni
        if (isset($data['password']) && !empty($data['password'])) {
            $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
            $user->setPassword($hashedPassword);
        }

        // Valider l'utilisateur (avec un groupe spécifique pour les mises à jour)
        $errors = $validator->validate($user, null, ['password_update']);

        // Retourner les erreurs de validation s'il y en a
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }

            return $this->json(['errors' => $errorMessages], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Sauvegarder les modifications dans la base de données
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json([
            'message' => 'Profil mis à jour avec succès',
            'user' => [
                'pseudo' => $user->getPseudo(),
                'description' => $user->getDescription(),
                'age' => $user->getAge(),
                'gender' => $user->getGender(),
                'discord' => $user->getDiscord(),
            ],
        ]);
    }



    #[Route('/login', name: 'login', methods: ['POST', 'GET'])]
    public function login(
        Request $request,
        UserRepository $userRepository,
        UserPasswordHasherInterface $userPasswordHasherInterface,
        JWTTokenManagerInterface $JWTManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        $user = $userRepository->findOneBy(['email' => $email]);

        if (!$user || !$userPasswordHasherInterface->isPasswordValid($user, $password)) {
            return $this->json(['message' => 'Email ou mot de passe incorrect'], Response::HTTP_UNAUTHORIZED);
        }

        $token = $JWTManager->create($user);

        // Inclure les informations utilisateur dans la réponse
        return $this->json([
            'token' => $token,
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'pseudo' => $user->getPseudo(),
            ]
        ], Response::HTTP_OK);
    }


    #[Route('/logout', name: 'logout', methods: ['POST'])]
    public function logout(): JsonResponse
    {
        return $this->json(['message' => 'Logout successful'], Response::HTTP_OK);
    }
}
