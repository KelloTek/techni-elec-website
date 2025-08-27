<?php

namespace App\DataFixtures;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class NotificationFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $notification = new Notification();
        $notification->setTitle($faker->sentence());
        $notification->setContent($faker->realText());
        $notification->setUser($this->getReference(UserFixtures::USER_REFERENCE . 'admin', User::class));
        $notification->setLink($faker->url());
        $notification->setStatus(false);
        $notification->setCreatedAt(new \DateTimeImmutable());

        $manager->persist($notification);

        for ($i = 0; $i <= 17; $i++) {
            $notification = new Notification();
            $notification->setTitle($faker->sentence());
            $notification->setContent($faker->realText());
            $notification->setUser($this->getReference(UserFixtures::USER_REFERENCE . rand(0, 31), User::class));
            $notification->setLink($faker->url());
            $notification->setStatus(false);
            $notification->setCreatedAt(new \DateTimeImmutable());

            $manager->persist($notification);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
