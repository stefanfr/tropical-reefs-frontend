<?php

namespace App\Repository\Manager;

use App\Entity\Manager\Product;
use App\Entity\Manager\ProductModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method ProductModel|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductModel|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductModel[]    findAll()
 * @method ProductModel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductModelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductModel::class);
    }

    public function save(ProductModel $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ProductModel $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
