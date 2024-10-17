<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
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
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(ArticleRepository $articleRepository, 
    SerializerInterface $serializerInterface): Response
    {
        $article = $articleRepository->findAll();
        $jsonArticle = $serializerInterface->serialize($article, 'json', ['groups' => 'article:read']);

        return new JsonResponse($jsonArticle, Response::HTTP_OK, [], true);
    }
    
    #[Route('/create', name: 'create', methods: ['POST'])]
    public function create(Request $request,
    EntityManagerInterface $entityManagerInterface, 
    SerializerInterface $serializerInterface,
    ValidatorInterface $validator): JsonResponse
    {
        $user = $this->getUser();

        $article = $serializerInterface->deserialize($request->getContent(), Article::class, 'json');
        $article->setDate(new \DateTime());
        $article->setUser($user);

        $errors = $validator->validate($article); 
        if (count($errors) > 0) {
            return new JsonResponse($errors, Response::HTTP_BAD_REQUEST);
        }


        $entityManagerInterface->persist($article);
        $entityManagerInterface->flush();

        $jsonArticle = $serializerInterface->serialize($article, 'json', ['groups' => 'article:create']);
        return new JsonResponse($jsonArticle, Response::HTTP_CREATED, [], true);

    }
}