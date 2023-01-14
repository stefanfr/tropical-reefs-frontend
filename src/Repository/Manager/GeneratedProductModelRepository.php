<?php

namespace App\Repository\Manager;

use App\Entity\Manager\GeneratedProductModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GeneratedProductModel>
 *
 * @method GeneratedProductModel|null find($id, $lockMode = null, $lockVersion = null)
 * @method GeneratedProductModel|null findOneBy(array $criteria, array $orderBy = null)
 * @method GeneratedProductModel[]    findAll()
 * @method GeneratedProductModel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GeneratedProductModelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GeneratedProductModel::class);
    }

    public function save(GeneratedProductModel $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(GeneratedProductModel $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return GeneratedProductModel[] Returns an array of GeneratedProductModel objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?GeneratedProductModel
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
