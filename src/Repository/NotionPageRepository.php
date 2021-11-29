<?php

namespace App\Repository;

use App\Entity\NotionPage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method NotionPage|null find($id, $lockMode = null, $lockVersion = null)
 * @method NotionPage|null findOneBy(array $criteria, array $orderBy = null)
 * @method NotionPage[]    findAll()
 * @method NotionPage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotionPageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NotionPage::class);
    }

    // /**
    //  * @return NotionPage[] Returns an array of NotionPage objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?NotionPage
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
