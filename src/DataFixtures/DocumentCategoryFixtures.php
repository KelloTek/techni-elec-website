<?php

namespace App\DataFixtures;

use App\Entity\DocumentCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class DocumentCategoryFixtures extends Fixture
{
    public const DOCUMENT_CATEGORY_REFERENCE = 'document_category_';

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 10; $i++) {
            $category = new DocumentCategory();
            $category->setLabel($faker->word());

            $this->addReference(self::DOCUMENT_CATEGORY_REFERENCE . $i, $category);

            $manager->persist($category);
        }

        $manager->flush();
    }
}
