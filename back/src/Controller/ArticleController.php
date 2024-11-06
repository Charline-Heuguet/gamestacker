<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\UserRepository;
use App\Repository\ArticleRepository;
use App\Service\CommentFilterService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
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

    /// VISUALISER TOUS LES ARTICLES ///
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        $article = $this->articleRepository->findAll();

        return $this->json($article, Response::HTTP_OK, [], ['groups' => 'article:read']);
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
        return $this->json( $article, 200, [], ['groups' => 'article:details', 'comment:details']);
    }

    

}