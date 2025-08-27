<?php

namespace App\DataFixtures;

use App\Entity\Certification;
use App\Entity\File;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CertificationFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 8; $i++) {
            $certification = new Certification();
            $certification->setName($faker->word());
            $certification->setLink($faker->url());
            $certification->setImage($this->getReference(FileFixtures::IMAGE_REFERENCE . rand(0, 4), File::class));

            $manager->persist($certification);
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
