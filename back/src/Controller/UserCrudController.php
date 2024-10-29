<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

//Controller exclusivement pour le profil d'un utilisateur
#[Route('/api/profile', name: 'api_')]
class UserCrudController extends AbstractController
{
    //On utilise le constructeur pour injecter les dépendances requises
    private $userRepository;
    private $router;

    public function __construct(UserRepository $userRepository,RouterInterface $router)
    {
        $this->userRepository = $userRepository;
        $this->router = $router;

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


    #[Route('/view-comments-article', name: 'comments_article', methods: ['GET'])]
public function viewCommentsArticle(): JsonResponse
{
    $user = $this->userRepository->find(218);

    if (!$user) {
        return $this->json(['message' => 'Cet utilisateur n\'existe pas'], 404);
    }

    $articleCommentsData = [];
    foreach ($user->getComment() as $comment) {
        if ($comment->getArticle() !== null) {
            $articleCommentsData[] = [
                'comment' => $comment,
                'url' => $this->router->generate('api_article_show', ['id' => $comment->getArticle()->getId()], RouterInterface::ABSOLUTE_URL)
            ];
        }
    }

    if (empty($articleCommentsData)) {
        return $this->json(['message' => 'Vous n\'avez pas encore posté de commentaires sur les articles'], 404);
    }

    return $this->json($articleCommentsData, 200, [], ['groups' => 'user:article:comments']);
}


    #[Route('/view-comments-forum', name: 'comments_forum', methods: ['GET'])]
    public function viewCommentsForum(): JsonResponse
    {
        $user = $this->userRepository->find(218);

        if (!$user) {
            return $this->json(['message' => 'Cet utilisateur n\'existe pas'], 404);
        }

        $forumCommentsData = [];
        foreach ($user->getComment() as $comment) {
            if ($comment->getForum() !== null) {
                $forumCommentsData[] = [
                    'comment' => $comment,
                    'url' => $this->router->generate('api_forum_view', ['id' => $comment->getForum()->getId()], RouterInterface::ABSOLUTE_URL)
                ];
            }
        }

        if (empty($forumCommentsData)) {
            return $this->json(['message' => 'Vous n\'avez pas encore posté de commentaires sur le forum'], 404);
        }

        return $this->json($forumCommentsData, 200, [], ['groups' => 'user:forum:comments']);
    }


    #[Route('/view-announcements', name: 'announcements', methods: ['GET'])]
public function viewAnnouncements(): JsonResponse
{
    $user = $this->userRepository->find(218);

    if (!$user) {
        return $this->json(['message' => 'Cet utilisateur n\'existe pas'], 404);
    }

    $announcements = $user->getAnnouncements();

    if ($announcements->isEmpty()) {
        return $this->json(['message' => 'Vous n\'avez pas encore posté d\'annonces'], 404);
    }

    // Créer un tableau contenant les annonces et leurs URLs
    $announcementData = [];
    foreach ($announcements as $announcement) {
        $announcementData[] = [
            'announcement' => $announcement,
            'url' => $this->router->generate('api_announcement_show', ['id' => $announcement->getId()], RouterInterface::ABSOLUTE_URL)
        ];
    }

    return $this->json($announcementData, 200, [], ['groups' => 'user:announcement']);
}




}