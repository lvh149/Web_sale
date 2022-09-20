<?php

namespace App\DataFixtures;

use App\Entity\OrdersDetail;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class OrdersDetailFixture extends Fixture implements DependentFixtureInterface
{
    public function getDependencies()
    {
        return [
            OrdersFixture::class,
            ProductsFixture::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 40; $i++) {
            $orderDetail = new OrdersDetail();
            $orderDetail->setOrderId($this->getReference(OrdersFixture::OrderId));
            $orderDetail->setProductId($this->getReference(ProductsFixture::ProductId));
            $orderDetail->setQuantity(random_int(1, 10));
            $orderDetail->setPrice(random_int(10, 100));
            $manager->persist($orderDetail);
        }
        $manager->flush();
    }
}
