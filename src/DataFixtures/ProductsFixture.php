<?php

namespace App\DataFixtures;

use App\Entity\Products;
use App\Entity\Categories;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductsFixture extends Fixture
{
    public const ProductId = 'ProductId';
    
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 40; $i++) {
            $product = new Products();
            $product->setCategoryId($this->getReference(CategoriesFixture::CategoryId));
            $product->setName('product '.$i);
            $product->setInfo('test');
            $product->setPrice(random_int(100, 999));
            $product->setPoint(random_int(100, 999));
            $product->setPointGive(random_int(0, 10));
            $manager->persist($product);
        }
        $this->addReference(self::ProductId, $product);
        $manager->flush();
    }
    

}
