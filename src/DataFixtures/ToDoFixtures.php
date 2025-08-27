<?php

namespace App\DataFixtures;

use App\Entity\ToDo;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ToDoFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 10; ++$i) {
            $todo = new ToDo();
            $todo->setContent($faker->realText());
            $todo->setStatus(false);
            $todo->setBeforeAt(new \DateTimeImmutable($faker->date()));

            $manager->persist($todo);
        }

        $manager->flush();
    }
}
