<?php

namespace App\Repository;

use App\Entity\TypeAnime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TypeAnime|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeAnime|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeAnime[]    findAll()
 * @method TypeAnime[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeAnimeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeAnime::class);
    }

    // /**
    //  * @return TypeAnime[] Returns an array of TypeAnime objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TypeAnime
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
