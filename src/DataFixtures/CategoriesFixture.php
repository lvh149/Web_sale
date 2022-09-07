<?php

namespace App\DataFixtures;

use App\Entity\Categories;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoriesFixture extends Fixture
{
    public const CategoryId = 'CategoryId';

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 20; $i++) {
            $category = new Categories();
            $category->setName('Category '.$i);            
            $manager->persist($category);    
            $this->setReference('category-id', $category);                  
        }
        $this->addReference(self::CategoryId, $category);

        $manager->flush();
        

    }
}
