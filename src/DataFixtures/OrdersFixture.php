<?php

namespace App\DataFixtures;

use App\Entity\Orders;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class OrdersFixture extends Fixture implements DependentFixtureInterface
{
    public const OrderId = 'OrderId';

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 40; $i++) {
            $order = new Orders();
            $order->setAdminId($this->getReference(UsersFixture::AdminId));
            $order->setCustomerId($this->getReference(UsersFixture::CustomerId));
            $order->setDate(\DateTime::createFromFormat('Y-m-d', "2022-09-07"));
            $order->setStatus(random_int(1, 3));
            $manager->persist($order);
        }
        $this->addReference(self::OrderId, $order);
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UsersFixture::class,
        ];
    }
}
