<?php
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
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
}