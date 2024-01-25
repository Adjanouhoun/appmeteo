<?php

namespace App\Repository;

use App\Entity\OpenWeatherMapForm;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OpenWeatherMapForm|null find($id, $lockMode = null, $lockVersion = null)
 * @method OpenWeatherMapForm|null findOneBy(array $criteria, array $orderBy = null)
 * @method OpenWeatherMapForm[]    findAll()
 * @method OpenWeatherMapForm[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OpenWeatherMapFormRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OpenWeatherMapForm::class);
    }

    // /**
    //  * @return OpenWeatherMapForm[] Returns an array of OpenWeatherMapForm objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OpenWeatherMapForm
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
