<?php

namespace App\Repository;

use App\Entity\Forum;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Forum>
 */
class ForumRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Forum::class);
    }
    #Ordonner les tickets par date
    public function findAllOrderedByDate(): Query
    {
        return $this->createQueryBuilder('f') #Alias l'entité Forum (f)
            ->orderBy('f.date', 'DESC') #Ordonner par date décroissante
            ->getQuery();
    }

    public function findBySearchTerm(string $term): Query #Rechercher un terme dans le titre
    {
        return $this->createQueryBuilder('f') #Alias l'entité Forum (f)
            ->where('f.title LIKE :term') #Rechercher le terme dans le titre	
            ->setParameter('term', '%' . $term . '%') #Paramètre du terme, prends en compte des caractères avant et après
            ->orderBy('f.date', 'DESC') #Ordonner par date décroissante
            ->getQuery();
    }

    // Afficher les 5 derniers tickets postés
    public function findLastFive(): array
    {
        return $this->createQueryBuilder('f') #Alias l'entité Forum (f)
            ->orderBy('f.date', 'DESC') #Ordonner par date décroissante
            ->setMaxResults(5) #Afficher les 5 derniers
            ->getQuery()
            ->getResult();
    }

    public function findForumsWithoutComments(): array
    {
        $twoWeeksAgo = new \DateTime();
        $twoWeeksAgo->modify('-2 weeks');

        return $this->createQueryBuilder('f')
            ->leftJoin('f.comment', 'c')
            ->andWhere('c.id IS NULL') // Vérifie l'absence de commentaires
            ->andWhere('f.date >= :twoWeeksAgo') // Vérifie la date
            ->setParameter('twoWeeksAgo', $twoWeeksAgo)
            ->orderBy('f.date', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }



    //    /**
    //     * @return Forum[] Returns an array of Forum objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('f.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Forum
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
