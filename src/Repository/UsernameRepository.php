<?php

namespace App\Repository;

use App\Entity\Username;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Username|null find($id, $lockMode = null, $lockVersion = null)
 * @method Username|null findOneBy(array $criteria, array $orderBy = null)
 * @method Username[]    findAll()
 * @method Username[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsernameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Username::class);
    }

    // /**
    //  * @return Username[] Returns an array of Username objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Username
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
