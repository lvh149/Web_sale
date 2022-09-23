<?php

namespace App\Repository;

use App\Entity\Categories;
use App\Entity\Products;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Products>
 *
 * @method Products|null find($id, $lockMode = null, $lockVersion = null)
 * @method Products|null findOneBy(array $criteria, array $orderBy = null)
 * @method Products[]    findAll()
 * @method Products[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Products::class);
    }

    public function add(Products $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Products $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Products[] Returns an array of Products objects
     */
    public function findByNameCategory($name): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.category', 'u')
            ->where('u.name = :name')
            ->setParameter('name', $name)
            ->andWhere('p.price IS NOT NULL')
            ->getQuery()
            ->getResult();
    }
    /**
     * @return Products[] Returns an array of Products objects
     */
    public function findProduct($category, $name, $min, $max)
    {
        return $this->createQueryBuilder('t')
        // ->select('c.id as parameters_id, t.id as products_id')
        ->innerJoin('t.category', 'c')
        ->andwhere('c.name = :category')
        ->setParameter('category', $category)
        ->andWhere('t.name LIKE :name')
        ->setParameter('name', '%'.$name.'%')
        ->andWhere('t.price BETWEEN :min AND :max')
        ->setParameter('min',$min)
        ->setParameter('max',$max)
        ->getQuery()
        ->getResult();
    }
    /**
     * @return Products[] Returns an array of Products objects
     */
    public function findProductPoint($name, $min, $max)
    {
        return $this->createQueryBuilder('t')
        // ->select('c.id as parameters_id, t.id as products_id')
        ->andWhere('t.price IS NULL')
        ->andWhere('t.name LIKE :name')
        ->setParameter('name', '%'.$name.'%')
        ->andWhere('t.point BETWEEN :min AND :max')
        ->setParameter('min',$min)
        ->setParameter('max',$max)
        ->getQuery()
        ->getResult();
    }

    public function findAllGreaterThanPrice(int $price)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT * FROM product p
            WHERE p.price > :price
            ORDER BY p.price ASC
            ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['price' => $price]);

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }

    public function checkParameterProducts($product_id,$parameter_id)
    {
        return $this->createQueryBuilder('t')
        // ->select('c.id as parameters_id, t.id as products_id')
        ->innerJoin('t.parameters', 'p')
        ->where('p.id = (:parameters_id)')
        ->setParameter('parameters_id', $parameter_id)
        ->andwhere('t.id = :product_id')
        ->setParameter('product_id', $product_id)
        ->getQuery()
        ->getResult();
    }


    //    public function findOneBySomeField($value): ?Products
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
