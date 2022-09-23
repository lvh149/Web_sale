<?php

namespace App\Repository;

use App\Entity\CartDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CartDetail>
 *
 * @method CartDetail|null find($id, $lockMode = null, $lockVersion = null)
 * @method CartDetail|null findOneBy(array $criteria, array $orderBy = null)
 * @method CartDetail[]    findAll()
 * @method CartDetail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CartDetailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CartDetail::class);
    }

    public function add(CartDetail $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CartDetail $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public function numberCart($cart_id): array
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(c.id) as number_product')
            ->andWhere('c.cart = :cart_id')
            ->setParameter('cart_id', $cart_id)
            ->getQuery()
            ->getResult();
    }

    public function SumPoint($cart_id): array
    {
        return $this->createQueryBuilder('c')
            ->select('SUM(p.point * c.quantity) as total_point')
            ->andWhere('c.cart = :cart_id')
            ->setParameter('cart_id', $cart_id)
            ->join('c.product', 'p')
            ->getQuery()
            ->getResult();
    }

    public function ClearCart($cart_id)
    {
        return $this->createQueryBuilder('c')
            ->delete()
            ->where('c.cart = :cart_id')
            ->setParameter('cart_id', $cart_id)
            ->getQuery()
            ->execute();
    }

    //    public function findOneBySomeField($value): ?CartDetail
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
