<?php

namespace App\Repository;

use Doctrine\ORM\Query;
use App\Entity\Announcement;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Announcement>
 */
class AnnouncementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Announcement::class);
    }


    // src/Repository/AnnouncementRepository.php

    public function findAllOrderedByDate(): Query
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.date', 'DESC')
            ->getQuery();
    }

    public function findBySearchTerm(string $term): Query
    {
        return $this->createQueryBuilder('a')
            ->where('a.roomId LIKE :term')
            ->orWhere('a.game LIKE :term')
            ->setParameter('term', '%' . $term . '%')
            ->orderBy('a.date', 'DESC')
            ->getQuery();
    }

    // Les dernieres annonces publiées
    public function findLatestAnnouncements(): array
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.date', 'DESC')
            ->setMaxResults(4)
            ->getQuery()
            ->getResult();
    }


public function findLatestAnnouncements(int $limit = 5)
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.date', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Announcement[] Returns an array of Announcement objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Announcement
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

}
