<?php

namespace App\DataFixtures;

use App\Entity\Users;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UsersFixture extends Fixture
{
    public const AdminId = 'AdminId';
    public const CustomerId = 'CustomerId';
    private $userPasswordHasherInterface;

    public function __construct (UserPasswordHasherInterface $userPasswordHasherInterface) 
    {
        $this->userPasswordHasherInterface = $userPasswordHasherInterface;
    }

    public function load(ObjectManager $manager): void
    {       

        for ($i = 0; $i < 20; $i++) {
            $user = new Users();
            $user->setName('user '.$i);
            $user->setEmail('email'.$i.'@gmail.com');
            $user->setPassword(
                $this->userPasswordHasherInterface->hashPassword(
                    $user, "123456"
                )
            );
            $user->setPhone('0'.random_int(100000000, 999999999));
            $user->setAddress('VN');
            $user->setRoles(['ROLE_ADMIN']);
            $manager->persist($user);
        }
        $this->addReference(self::AdminId, $user);
        for ($i = 41; $i < 60; $i++) {
            $user = new Users();
            $user->setName('user '.$i);
            $user->setEmail('email'.$i.'@gmail.com');
            $user->setPassword(
                $this->userPasswordHasherInterface->hashPassword(
                    $user, "123456"
                )
            );
            $user->setPhone('0'.random_int(100000000, 999999999));
            $user->setAddress('VN');
            $user->setRoles(['ROLE_SUPERADMIN']);
            $manager->persist($user);
        }    
        for ($i = 21; $i < 40; $i++) {
            $user = new Users();
            $user->setName('user '.$i);
            $user->setEmail('email'.$i.'@gmail.com');
            $user->setPassword(
                $this->userPasswordHasherInterface->hashPassword(
                    $user, "123456"
                )
            );
            $user->setPhone('0'.random_int(100000000, 999999999));
            $user->setAddress('VN');
            $user->setRoles(['ROLE_CUSTOMER']);
            $manager->persist($user);
        }
        $this->addReference(self::CustomerId, $user);


        $manager->flush();
    }
}
