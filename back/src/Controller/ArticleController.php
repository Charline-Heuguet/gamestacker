<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\AnnouncementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
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
        // Récupération de l'ID de la catégorie à partir des paramètres de requête
        $categoryId = $request->query->get('category');

        // Vérifie si un ID de catégorie est fourni, sinon retourne tous les articles
        if ($categoryId) {
            $articles = $this->articleRepository->findByCategory($categoryId);
        } else {
            $articles = $this->articleRepository->findAll();
        }

        return $this->json($articles, Response::HTTP_OK, [], ['groups' => 'article:read']);
    }

    #[Route('/announce', name: 'announce', methods: ['GET'])]
    public function fetchAnnounce(AnnouncementRepository $announcementRepository): Response
    {
        $announce = $announcementRepository->findLatestAnnouncements(5);
        return $this->json($announce, 200, [], ['groups' => 'announcement:read']);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function viewArticle($id): Response
    {
        $article = $this->articleRepository->find($id);
        return $this->json($article, 200, [], ['groups' => 'article:details', 'comment:details']);
    }
}
