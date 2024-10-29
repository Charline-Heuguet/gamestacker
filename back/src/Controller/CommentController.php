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
    public function commentForumUpvote($id, UserRepository $userRepository, CommentRepository $commentRepository, Request $request): JsonResponse
    {
        $user = $userRepository->findOneBy(['id' => 217]);
        if (!$user) {
            return new JsonResponse(['message' => 'Utilisateur non trouvé'], 403);
        }

        $comment = $commentRepository->find($id);
        if (!$comment) {
            return new JsonResponse(['message' => 'Commentaire non trouvé'], 404);
        }

        $session = $request->getSession();
        $upvotedComments = $session->get('upvoted_comments', []);

        if (in_array($comment->getId(), $upvotedComments)) {
            // Annuler l'upvote
            $comment->setUpvote($comment->getUpvote() - 1);
            $upvotedComments = array_diff($upvotedComments, [$comment->getId()]);
        } else {
            // Ajouter un upvote
            $comment->setUpvote($comment->getUpvote() + 1);
            $upvotedComments[] = $comment->getId();
        }

        $session->set('upvoted_comments', $upvotedComments);
        $this->entityManager->flush();
        return new JsonResponse(['upvotes' => $comment->getUpvote()]);
        
    }
    
    //Ajouter -1 au nombre de votes sur un commentaire
    #[Route('/comment/{id}/downvote', name: 'comment_downvote', methods: ['POST'])]
    public function commentForumDownvote($id, UserRepository $userRepository, CommentRepository $commentRepository, EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        $user = $userRepository->findOneBy(['id' => 217]); // Remplacez 218 par l'ID de l'utilisateur actuel
        if (!$user) {
            return new JsonResponse(['message' => 'Connectez-vous pour participer'], 403);
        }

        $comment = $commentRepository->find($id);
        if (!$comment) {
            return new JsonResponse(['message' => 'Commentaire non trouvé'], 404);
        }

        $session = $request->getSession();
        $downvotedComments = $session->get('downvoted_comments', []);

        if (in_array($comment->getId(), $downvotedComments)) {
            // Annuler le downvote
            $comment->setDownvote($comment->getDownvote() + 1);
            $downvotedComments = array_diff($downvotedComments, [$comment->getId()]);
        } else {
            // Ajouter un downvote
            $comment->setDownvote($comment->getDownvote() - 1);
            $downvotedComments[] = $comment->getId();
        }

        $session->set('downvoted_comments', $downvotedComments);
        $entityManager->flush();
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
        $userTest = $userRepository->findOneBy(['id' => 218]);
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