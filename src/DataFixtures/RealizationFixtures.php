<?php

namespace App\DataFixtures;

use App\Entity\File;
use App\Entity\Realization;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class RealizationFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 10; $i++) {
            $realization = new Realization();
            $realization->setTitle($faker->sentence(3));
            $realization->setContent($faker->paragraph(2));

            for ($j = 0; $j < rand(1, 3); $j++) {
                $fileReference = FileFixtures::IMAGE_REFERENCE . rand(1, 5);
                if ($this->hasReference($fileReference, File::class)) {
                    $file = $this->getReference($fileReference, File::class);
                    $realization->addImage($file);
                }
            }

            $manager->persist($realization);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            FileFixtures::class,
        ];
    }
}
