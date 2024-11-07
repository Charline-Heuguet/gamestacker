<?php


// src/Repository/ArticleRepository.php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    // Méthode pour récuperer les 5 derniers articles:
    public function findLastFiveArticles(): array
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.date', 'DESC')
            ->setMaxResults(6)
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère les articles d'une catégorie spécifique
     *
     * @param int $categoryId
     * @return Article[]
     */
    public function findByCategory(int $categoryId): array
    {
        return $this->createQueryBuilder('a')
            ->join('a.category', 'c')
            ->andWhere('c.id = :categoryId')
            ->setParameter('categoryId', $categoryId)
            ->getQuery()
            ->getResult();
    }
}
