<?php

namespace App\DataFixtures;

use App\Entity\Orders;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class OrdersFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 40; $i++) {
            $order = new Orders();
            //
            $order->setDate('2022-06-09');
            $order->setStatus(random_int(1, 3));
            $manager->persist($order);
        }
        $manager->flush();
    }
}
