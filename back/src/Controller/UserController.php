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
    #[Route('/signup',name:'signup', methods: ['POST'])]
    public function signup(Request $request, 
    SerializerInterface $serialiazerInterface,
    UserPasswordHasherInterface $passwordHasherInterface,
    ValidatorInterface $validatorInterface,
    EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $serialiazerInterface->deserialize(
            $request->getContent(), 
            User::class, 
            'json', 
            ['groups' => 'user:signup']
        );

        $user->setPassword($passwordHasherInterface->hashPassword($user, $user->getPassword()));

        $currentRole = $user->getRoles();
        if(!in_array('ROLE_USER', $currentRole)){
            $user->setRoles(array_merge($currentRole, ['ROLE_USER']));
        }

        $errors = $validatorInterface->validate($user);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        // Persister et enregistrer l'objet User
        $entityManager->persist($user);
        $entityManager->flush();

        // Retourner l'utilisateur créé
        return $this->json($user, Response::HTTP_CREATED, [], ['groups' => 'user:signup']);
    }

    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(Request $request,
    UserRepository $userRepository, 
    UserPasswordHasherInterface $userPasswordHasherInterface,
    JWTTokenManagerInterface $JWTManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        $user = $userRepository->findOneBy(['email' => $email]);

        if (!$user || !$userPasswordHasherInterface->isPasswordValid($user, $password)) {
            return $this->json(['message' => 'Email ou mot de passe incorrect'], Response::HTTP_UNAUTHORIZED);
        }

        $token = $JWTManager->create($user);

        // Retourner le token JWT
        return $this->json(['token' => $token], Response::HTTP_OK);


    }

    #[Route('/logout', name: 'logout', methods: ['POST'])]
    public function logout(): JsonResponse
    {
        return $this->json(['message' => 'Logout successful'], Response::HTTP_OK);
    }
    
}