<?php

namespace App\DataFixtures;

use App\Entity\Admins;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class AdminsFixture extends Fixture
{

    public function load(ObjectManager $manager): void
    {       

        for ($i = 0; $i < 20; $i++) {
            $admin = new Admins();
            $plaintextPassword = 123456;
            // $password = $passwordHasher->hashPassword($plaintextPassword);
            $admin->setName('admin '.$i);
            $admin->setEmail('email'.$i.'@gmail.com');
            $admin->setPassword($plaintextPassword);
            $admin->setPhone('0'.random_int(100000000, 999999999));
            $admin->setAddress('VN');
            $admin->setRole(random_int(0,1));
            $manager->persist($admin);
        }


        $manager->flush();
    }
}
