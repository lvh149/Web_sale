<?php

namespace App\DataFixtures;

use App\Entity\Products;
use App\Entity\Categories;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProductsFixture extends Fixture implements DependentFixtureInterface
{
    public const ProductId = 'ProductId';

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 40; $i++) {
            $product = new Products();
            $product->setCategory($this->getReference(CategoriesFixture::CategoryId));
            $product->setName('product '.$i);
            $product->setInfo('test');
            $product->setPrice(random_int(100, 999));
            $product->setPoint(random_int(100, 999));
            $product->setPointGive(random_int(0, 10));
            $manager->persist($product);
            $product->addParameter($this->getReference(ParametersFixture::ParamId));
        }
        $this->addReference(self::ProductId, $product);
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ParametersFixture::class,
        ];
    }

}
