<?php

namespace App\DataFixtures;

use App\Entity\Products;
use App\Entity\Categories;
use App\Entity\Parameters;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ParametersFixture extends Fixture
{
    public const ParamId = 'ParamId';

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 5; $i++) {
            $param = new Parameters();
            // $product->setCategory($this->getReference(CategoriesFixture::CategoryId));
            $param->setValue('Size '.$i);
            $manager->persist($param);
        }
        $this->addReference(self::ParamId, $param);
        $manager->flush();
    }


}
