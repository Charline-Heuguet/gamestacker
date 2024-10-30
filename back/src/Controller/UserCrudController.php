<?php

namespace App\Controller;

use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

//Controller exclusivement pour le profil d'un utilisateur
#[Route('/api/profile', name: 'api_')]
class UserCrudController extends AbstractController
{
    //On utilise le constructeur pour injecter les dépendances requises
    private $userRepository;
    private $router;
    private $entityManager;

    public function __construct(UserRepository $userRepository,
    RouterInterface $router, 
    EntityManagerInterface $entityManager)
    {
        $this->userRepository = $userRepository;
        $this->router = $router;
        $this->entityManager = $entityManager;

    }

    #[Route('/', name: 'profile', methods: ['GET'])]
    public function viewProfile(): JsonResponse
    {
        //a remplacer par $user = $this->getUser() pour récuperer
        //un utilisateur connecter et existant.
        $user = $this->userRepository->findBy(['id' => 218]);

        if (!$user) {
            return $this->json(['message' => 'Cet utilisateur n\'existe pas'], 404);
        }
        //On récupère et retourne les informations qu'un utilisateur veut voir grâce
        //aux groupe de sérialisation
        return $this->json($user, 200, [], ['groups' => 'user:read']);
    }

    #[Route('/verify-password', name: 'verify_password', methods: ['POST'])]
    public function verifyPassword(Request $request, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $user = $this->userRepository->find(241);

        if (!$user) {
            return $this->json(['message' => 'Utilisateur non connecté'], 403);
        }

        $data = json_decode($request->getContent(), true);
        $password = $data['password'] ?? '';

        // Vérification du mot de passe
        if (!$passwordHasher->isPasswordValid($user, $password)) {
            return $this->json(['message' => 'Mot de passe incorrect'], 403);
        }

        // Redirection vers le formulaire de modification
        return $this->json([
            'message' => 'Mot de passe vérifié',
            'redirect_url' => $this->generateUrl('api_edit_profile')
        ], 200);
    }

    #[Route('/edit', name: 'edit_profile', methods: ['PUT'])]
    public function editProfile(Request $request): JsonResponse
    {
        // Récupérer un utilisateur connecté et existant
        $user = $this->userRepository->find(218);

        if (!$user) {
            return $this->json(['message' => 'Cet utilisateur n\'existe pas'], 404);
        }

        $data = json_decode($request->getContent(), true);
        $form = $this->createForm(UserType::class, $user);

        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            return $this->json(['message' => 'Profil mis à jour'], 200);
        }

        $errors = [];
    foreach ($form->getErrors(true) as $error) {
        $errors[] = $error->getMessage();
    }

        return $this->json(['message' => 'Données non valides', 'errors' => $errors], 400);
    }

    //Permet de voir les commentaires des articles posté par un utilisateur
    #[Route('/view-comments-article', name: 'comments_article', methods: ['GET'])]
    public function viewCommentsArticle(): JsonResponse
    {
        //On récupère l'utilisateur
        $user = $this->userRepository->find(218);

        //On vérifie que l'utilisateur existe sinon on renvoi une 404 
        if (!$user) {
            return $this->json(['message' => 'Cet utilisateur n\'existe pas'], 404);
        }

        //On crée un tableau pour stocker les commentaires des articles et l'url de l'article
        $articleCommentsData = [];
        //On parcours les commentaires de l'utilisateur
        foreach ($user->getComment() as $comment) {
            //Si l'article n'est pas null alors j'insère le commentaire et l'url de l'article dans le tableau $articleCommentsData
            if ($comment->getArticle() !== null) {
                $articleCommentsData[] = [
                    'comment' => $comment,
                    //!\Bien vérfier la route (name) et les paramètres à passers
                    'url' => $this->router->generate('api_article_show', ['id' => $comment->getArticle()->getId()], RouterInterface::ABSOLUTE_URL)
                ];
            }
        }

        //Si le tableau est vide alors on renvoi un message d'erreur
        if (empty($articleCommentsData)) {
            return $this->json(['message' => 'Vous n\'avez pas encore posté de commentaires sur les articles'], 404);
        }

        return $this->json($articleCommentsData, 200, [], ['groups' => 'user:article:comments']);
    }

    //Permet de voir les commentaires des forums posté par un utilisateur
    #[Route('/view-comments-forum', name: 'comments_forum', methods: ['GET'])]
    public function viewCommentsForum(): JsonResponse
    {
        //On récupère l'utilisateur
        $user = $this->userRepository->find(218);

        //On vérifie que l'utilisateur existe, sinon on renvoi une 404
        if (!$user) {
            return $this->json(['message' => 'Cet utilisateur n\'existe pas'], 404);
        }

        
        $forumCommentsData = []; // On créer un tableau $forumCommentsData pour stocker les commentaire et l'url
        foreach ($user->getComment() as $comment) { // On parcours les commentaires de l'utilisateur et on génère l'url du ticket de forum pour chaque commentaire
            if ($comment->getForum() !== null) { //Si le forum récuperé par les commentaire n'est pas null
                $forumCommentsData[] = [ //Je stocke les commentaires de chaque forum et je génère l'url correspondante
                    'comment' => $comment,
                    'url' => $this->router->generate('api_forum_view', ['id' => $comment->getForum()->getId()], RouterInterface::ABSOLUTE_URL)
                ];
            }
        }

        //Si le tableau $forumCommentsData est vide alors on renvoi un message d'erreur
        if (empty($forumCommentsData)) {
            return $this->json(['message' => 'Vous n\'avez pas encore posté de commentaires sur le forum'], 404);
        }

        return $this->json($forumCommentsData, 200, [], ['groups' => 'user:forum:comments']);
    }

    //PERMET DE VOIR LES ANNONCES POSTÉ PAR UN UTILISATEUR SUR SON PROFIL
    //Modification possible en utilisant des filtre ex: afficher les 5 derniers
    #[Route('/view-announcements', name: 'announcements', methods: ['GET'])]
    public function viewAnnouncements(): JsonResponse
    {
        //On récupère l'utilisateur
        $user = $this->userRepository->find(218);

        //On vérifie que l'utilisateur existe, sinon on renvoi une 404
        if (!$user) {
            return $this->json(['message' => 'Cet utilisateur n\'existe pas'], 404);
        }

        //On récupère les annonces de l'utilisateur
        $announcements = $user->getAnnouncements();

        //Si l'utilisateur n'a pas encore posté d'annonces alors on renvoi un message d'erreur
        if ($announcements->isEmpty()) {
            return $this->json(['message' => 'Vous n\'avez pas encore posté d\'annonces'], 404);
        }

        // Créer un tableau contenant les annonces et leurs URLs
        $announcementData = [];
        //Pour chaque annonce, on stocke l'annonce et l'url de l'annonce dans le tableau $announcementData
        foreach ($announcements as $announcement) {
            $announcementData[] = [
                'announcement' => $announcement,
                'url' => $this->router->generate('api_announcement_show', ['id' => $announcement->getId()], RouterInterface::ABSOLUTE_URL)
            ];
        }

        return $this->json($announcementData, 200, [], ['groups' => 'user:announcement']);
    }
}