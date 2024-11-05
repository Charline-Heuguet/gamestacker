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
        // Récupère toutes les catégories
        $categories = $categoryRepository->findAll();

        // Retourne les catégories en JSON avec le groupe "article:read"
        return $this->json($categories, Response::HTTP_OK, [], ['groups' => 'article:read']);
    }
}
