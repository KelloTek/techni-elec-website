<?php

namespace App\DataFixtures;

use App\Entity\RequestCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class RequestCategoryFixtures extends Fixture
{
    public const REQUEST_CATEGORY_REFERENCE = 'request_category_';

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 6; $i++) {
            $requestCategory = new RequestCategory();
            $requestCategory->setLabel($faker->unique()->word());

            $this->addReference(self::REQUEST_CATEGORY_REFERENCE . $i, $requestCategory);

            $manager->persist($requestCategory);
        }

        $manager->flush();
    }
}
