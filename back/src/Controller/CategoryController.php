<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/categories', name: 'api_categories_')]
class CategoryController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository): Response
    {
        // Utilise la nouvelle méthode pour récupérer uniquement les catégories avec articles
        $categories = $categoryRepository->findCategoriesWithArticles();

        return $this->json($categories, Response::HTTP_OK, [], ['groups' => 'article:read']);
    }
}
