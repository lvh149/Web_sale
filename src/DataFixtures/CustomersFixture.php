<?php

namespace App\DataFixtures;

use App\Entity\Customers;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CustomersFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 20; $i++) {
            $customer = new Customers();
            $plaintextPassword = 123456;
            // $password = $passwordHasher->hashPassword($plaintextPassword);
            $customer->setName('customer '.$i);
            $customer->setEmail('email'.$i.'@gmail.com');
            $customer->setPassword($plaintextPassword);
            $customer->setPhone('0'.random_int(100000000, 999999999));
            $customer->setAddress('VN');
            $manager->persist($customer);
        }


        $manager->flush();
    }
}
