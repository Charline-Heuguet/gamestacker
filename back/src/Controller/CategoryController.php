<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('api/category', name: 'app_category_')]
class CategoryController extends AbstractController
{
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        $categories = $this->categoryRepository->findAll();
        return $this->json($categories, Response::HTTP_OK, [], ['groups' => 'category:index']);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show($id): Response
    {
        $category = $this->categoryRepository->find($id);
        return $this->json($category, Response::HTTP_OK, [], ['groups' => 'category:show']);
    }
}
