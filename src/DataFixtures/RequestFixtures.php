<?php

namespace App\DataFixtures;

use App\Entity\Request;
use App\Entity\RequestCategory;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class RequestFixtures extends Fixture implements DependentFixtureInterface
{
    public const REQUEST_REFERENCE = 'request_';

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 8; $i++) {
            $request = new Request();
            $request->setTitle($faker->unique()->word());
            $request->setContent($faker->realText());
            $request->setCategory($this->getReference(RequestCategoryFixtures::REQUEST_CATEGORY_REFERENCE . rand(0, 5), RequestCategory::class));
            $request->setUser($this->getReference(UserFixtures::USER_REFERENCE . rand(0, 31), User::class));
            $request->setCreatedAt();

            $this->addReference(self::REQUEST_REFERENCE . $i, $request);

            $manager->persist($request);
        }

        $manager->flush();
    }

    public function getDependencies(): array {
        return [
            RequestCategoryFixtures::class,
            UserFixtures::class,
        ];
    }
}
