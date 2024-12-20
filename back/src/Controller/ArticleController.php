<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AnnouncementRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('api/article', name: 'api_article_')]
class ArticleController extends AbstractController
{
    private $entityManager;
    private $articleRepository;

    public function __construct(EntityManagerInterface $entityManager, ArticleRepository $articleRepository)
    {
        $this->entityManager = $entityManager;
        $this->articleRepository = $articleRepository;
    }

    /// VISUALISER TOUS LES ARTICLES AVEC FILTRE CATEGORIE ///
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $categoryId = $request->query->get('category');

        if ($categoryId) {
            $articles = $this->articleRepository->findByCategory($categoryId);
        } else {
            $articles = $this->articleRepository->findAll();
        }

        return $this->json($articles, Response::HTTP_OK, [], ['groups' => 'article:read']);
    }

    #[Route('/{id}/recommendations', name: 'article_recommendations', methods: ['GET'])]
    public function getArticleRecommendations(Article $article): JsonResponse
    {
        $recommendedArticles = $this->articleRepository->findArticlesByCommonCategories($article);

        return $this->json($recommendedArticles, 200, [], ['groups' => 'article:read']);
    }

    #[Route('/announce', name: 'announce', methods: ['GET'])]
    public function fetchAnnounce(AnnouncementRepository $announcementRepository): Response
    {
        $announce = $announcementRepository->findLatestAnnouncements(5);
        return $this->json($announce, 200, [], ['groups' => 'announcement:read']);
    }

    // Pour avoir les 5 derniers articles
    #[Route('/last-articles', name: 'last', methods: ['GET'])]
    public function lastFiveArticles(): Response
    {
        $articles = $this->articleRepository->findLastFiveArticles();
        return $this->json($articles, 200, [], ['groups' => 'article:latest']);
    }


    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function viewArticle($id): Response
    {
        $article = $this->articleRepository->find($id);
        return $this->json($article, 200, [], ['groups' => 'article:details', 'comment:details']);
    }
}
