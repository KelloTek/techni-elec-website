<?php

namespace App\DataFixtures;

use App\Entity\Discussion;
use App\Entity\Request;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class DiscussionFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $discussion = new Discussion();
        $discussion->setRequest($this->getReference(RequestFixtures::REQUEST_REFERENCE . rand(0, 7), Request::class));
        $discussion->setUser($this->getReference(UserFixtures::USER_REFERENCE . 'admin', User::class));
        $discussion->setContent($faker->realText());
        $discussion->setCreatedAt();

        $manager->persist($discussion);

        for ($i = 0; $i < 10; $i++) {
            $discussion = new Discussion();
            $discussion->setRequest($this->getReference(RequestFixtures::REQUEST_REFERENCE . rand(0, 7), Request::class));
            $discussion->setUser($this->getReference(UserFixtures::USER_REFERENCE . rand(0, 31), User::class));
            $discussion->setContent($faker->realText());
            $discussion->setCreatedAt();

            $manager->persist($discussion);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            RequestFixtures::class,
            UserFixtures::class,
        ];
    }
}
