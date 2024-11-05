<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * Récupère les catégories ayant au moins un article
     *
     * @return Category[]
     */
    public function findCategoriesWithArticles(): array
    {
        return $this->createQueryBuilder('c')
            ->join('c.articles', 'a')
            ->andWhere('a IS NOT NULL')
            ->getQuery()
            ->getResult();
    }
}
