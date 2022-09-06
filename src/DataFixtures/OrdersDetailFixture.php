<?php

namespace App\DataFixtures;

use App\Entity\OrdersDetail;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class OrdersDetailFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 40; $i++) {
            $orderDetail = new OrdersDetail();
            //
            $orderDetail->setQuantity(random_int(1, 10));
            $manager->persist($orderDetail);
        }
        $manager->flush();
    }
}
