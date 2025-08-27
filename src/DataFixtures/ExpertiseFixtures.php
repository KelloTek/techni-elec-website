<?php

namespace App\DataFixtures;

use App\Entity\Expertise;
use App\Entity\File;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ExpertiseFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 10; $i++) {
            $expertise = new Expertise();
            $expertise->setName($faker->word());
            $expertise->setImage($this->getReference(FileFixtures::IMAGE_REFERENCE. rand(0, 4), File::class));

            $manager->persist($expertise);
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
