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

    // Ajouter +1 au nombre de votes sur un commentaire
    #[Route('/comment/{id}/upvote', name: 'comment_upvote', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function commentForumUpvote($id, CommentRepository $commentRepository, Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['message' => 'Utilisateur non connecté'], 403);
        }
    
        $comment = $commentRepository->find($id);
        if (!$comment) {
            return new JsonResponse(['message' => 'Commentaire non trouvé'], 404);
        }
    
        $session = $request->getSession();
        $upvotedComments = $session->get('upvoted_comments', []);
    
        if (in_array($comment->getId(), $upvotedComments)) {
            return new JsonResponse(['message' => 'Vous avez déjà voté pour ce commentaire'], 400);
        }
    
        $comment->setUpvote($comment->getUpvote() + 1);
        $upvotedComments[] = $comment->getId();
        $session->set('upvoted_comments', $upvotedComments);
    
        try {
            $this->entityManager->persist($comment);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Erreur lors de la mise à jour : ' . $e->getMessage()], 500);
        }
    
        return new JsonResponse(['upvotes' => $comment->getUpvote()]);
    }




    // Ajouter -1 au nombre de votes sur un commentaire
    #[Route('/comment/{id}/downvote', name: 'comment_downvote', methods: ['POST'])]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
public function commentForumDownvote($id, CommentRepository $commentRepository, Request $request): JsonResponse
{
    $user = $this->getUser();
    if (!$user) {
        return new JsonResponse(['message' => 'Connectez-vous pour participer'], 403);
    }

    $comment = $commentRepository->find($id);
    if (!$comment) {
        return new JsonResponse(['message' => 'Commentaire non trouvé'], 404);
    }

    $session = $request->getSession();
    $upvotedComments = $session->get('upvoted_comments', []);
    $downvotedComments = $session->get('downvoted_comments', []);

    if (in_array($comment->getId(), $downvotedComments)) {
        $comment->setDownvote($comment->getDownvote() - 1);
        $downvotedComments = array_diff($downvotedComments, [$comment->getId()]);
    } else {
        $comment->setDownvote($comment->getDownvote() + 1);
        $downvotedComments[] = $comment->getId();
        if (in_array($comment->getId(), $upvotedComments)) {
            $comment->setUpvote($comment->getUpvote() - 1);
            $upvotedComments = array_diff($upvotedComments, [$comment->getId()]);
        }
    }

    $session->set('upvoted_comments', $upvotedComments);
    $session->set('downvoted_comments', $downvotedComments);
    try {
        $this->entityManager->persist($comment);
        $this->entityManager->flush();
    } catch (\Exception $e) {
        return new JsonResponse(['message' => 'Erreur lors de la mise à jour : ' . $e->getMessage()], 500);
    }

    return new JsonResponse(['upvotes' => $comment->getUpvote(), 'downvotes' => $comment->getDownvote()]);
}

    

    // Ajouter un commentaire à un article
    #[Route('/comment/{id}/add-article', name: 'add_article_comment', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function addArticleComment(Request $request, int $id, CommentFilterService $commentFilterService, ArticleRepository $articleRepository): JsonResponse
    {
        $article = $articleRepository->find($id);
        if (!$article) {
            return new JsonResponse(['status' => 'Article non trouvé'], 404);
        }

        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['status' => 'Utilisateur non connecté'], 403);
        }

        $data = json_decode($request->getContent(), true);

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            $filteredContent = $commentFilterService->filterProhibitedContent($comment->getContent());
            $comment->setContent($filteredContent);
            $comment->setDate(new \DateTime());
            $comment->setUser($user);
        } else {
            return new JsonResponse(['status' => 'Invalid data'], 400);
        }

        $article->addComment($comment);
        $this->entityManager->persist($comment);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'Comment added successfully'], 201);
    }

    // Ajouter un commentaire à un forum
    #[Route('/comment/{id}/add-forum', name: 'add_forum_comment', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function addForumComment(Request $request, int $id, CommentFilterService $commentFilterService, ForumRepository $forumRepository): JsonResponse
    {
        $forum = $forumRepository->find($id);
        if (!$forum) {
            return new JsonResponse(['status' => 'Forum non trouvé'], 404);
        }

        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['status' => 'Utilisateur non connecté'], 403);
        }

        $data = json_decode($request->getContent(), true);
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            $filteredContent = $commentFilterService->filterProhibitedContent($comment->getContent());
            $comment->setContent($filteredContent);
            $comment->setDate(new \DateTime());
            $comment->setUser($user);
        } else {
            return new JsonResponse(['status' => 'Invalid data'], 400);
        }

        $forum->addComment($comment);
        $this->entityManager->persist($comment);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'Comment added successfully'], 201);
    }
}
