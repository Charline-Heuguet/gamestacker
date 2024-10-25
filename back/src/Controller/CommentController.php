<?php

namespace App\Controller;

use App\Entity\Forum;
use App\Entity\Article;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use App\Repository\UserRepository;
use App\Repository\CommentRepository;
use App\Repository\ForumRepository;
use App\Service\CommentFilterService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


// #[IsGranted('ROLE_USER')]
#[Route('/api')]
class CommentController extends AbstractController
{

    private $entityManager;
    private $commentRepository;

    public function __construct(EntityManagerInterface $entityManager, CommentRepository $commentRepository)
    {
        $this->entityManager = $entityManager;
        $this->commentRepository = $commentRepository;
    }

    
    //Ajouter +1 au nombre de votes sur un commentaire 
    #[Route('/comment/{id}/upvote', name: 'comment_upvote', methods: ['POST'])]
    public function commentForumUpvote($id, UserRepository $userRepository): JsonResponse
    {
        $user = $userRepository->findOneBy(['id' => 217]);
        if (!$user) {
            return new JsonResponse(['message' => 'Connectez-vous pour participer'], 403);
        }
    
        $comment = $this->commentRepository->find($id);
        if (!$comment) {
            return new JsonResponse(['message' => 'Commentaire non trouvé'], 404);
        }
    
        if ($comment->getUpvote() > 0) {
            $comment->setUpvote($comment->getUpvote() - 1);
        } else {
            $comment->setUpvote($comment->getUpvote() + 1);
        }

        $this->entityManager->flush();
        return new JsonResponse(['upvotes' => $comment->getUpvote()]);
        
    }
    
    //Ajouter -1 au nombre de votes sur un commentaire
    #[Route('/comment/{id}/downvote', name: 'comment_downvote', methods: ['POST'])]
    public function commentForumDownvote($id, UserRepository $userRepository): JsonResponse
    {
        $user = $userRepository->findOneBy(['id' => 217]);
        if (!$user) {
            return new JsonResponse(['message' => 'Connectez-vous pour participer'], 403);
        }
    
        $comment = $this->commentRepository->find($id);
        if (!$comment) {
            return new JsonResponse(['message' => 'Commentaire non trouvé'], 404);
        }
    
        if ($comment->getDownvote() < 0) {
        $comment->setDownvote($comment->getDownvote() + 1);
    } else {
        $comment->setDownvote($comment->getDownvote() - 1);
    }
    
        $this->entityManager->flush();
        return new JsonResponse(['downvotes' => $comment->getDownvote()]);
    }


    #[Route('/comment/{id}/add-article', name: 'add_article_comment', methods: ['POST'])]
    public function addArticleComment(Request $request, int $id, UserRepository $userRepository, CommentFilterService $commentFilterService, ArticleRepository $articleRepository): JsonResponse
    {
        // Récupérer l'article par ID
        $article = $articleRepository->find($id);
        if (!$article) {
            return new JsonResponse(['status' => 'Article non trouvé'], 404);
        }

        // Utilisateur test temporaire
        $userTest = $userRepository->findOneBy(['id' => 211]);
        if (!$userTest) {
            return new JsonResponse(['status' => 'Utilisateur non trouvé'], 404);
        }

        // Récupérer les données du commentaire depuis la requête
        $data = json_decode($request->getContent(), true);

        // Créer et valider le formulaire de commentaire
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            $filteredContent = $commentFilterService->filterProhibitedContent($comment->getContent());
            $comment->setContent($filteredContent);
            $comment->setDate(new \DateTime());
            $comment->setUser($userTest);
        } else {
            return new JsonResponse(['status' => 'Invalid data'], 400);
        }

        // Associer le commentaire à l'article
        $article->addComment($comment);

        // Persister les changements
        $this->entityManager->persist($comment);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'Comment added successfully'], 201);
    }

    #[Route('/comment/{id}/add-forum', name: 'add_forum_comment', methods: ['POST'])]
    public function addForumComment(Request $request, int $id, UserRepository $userRepository, CommentFilterService $commentFilterService, ForumRepository $forumRepository): JsonResponse
    {
        // Récupérer l'article par ID
        $forum = $forumRepository->find($id);
        if (!$forum) {
            return new JsonResponse(['status' => 'Ticket non trouvé'], 404);
        }

        // Utilisateur test temporaire
        $userTest = $userRepository->findOneBy(['id' => 211]);
        if (!$userTest) {
            return new JsonResponse(['status' => 'Utilisateur non trouvé'], 404);
        }

        // Récupérer les données du commentaire depuis la requête
        $data = json_decode($request->getContent(), true);

        // Créer et valider le formulaire de commentaire
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            $filteredContent = $commentFilterService->filterProhibitedContent($comment->getContent());
            $comment->setContent($filteredContent);
            $comment->setDate(new \DateTime());
            $comment->setUser($userTest);
        } else {
            return new JsonResponse(['status' => 'Invalid data'], 400);
        }

        // Associer le commentaire à l'article
        $forum->addComment($comment);

        // Persister les changements
        $this->entityManager->persist($comment);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'Comment added successfully'], 201);
    }
}