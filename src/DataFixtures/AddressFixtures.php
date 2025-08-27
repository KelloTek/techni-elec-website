<?php

namespace App\DataFixtures;

use App\Entity\Address;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AddressFixtures extends Fixture
{
    public const ADDRESS_REFERENCE = 'address_';

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $address = new Address();
        $address->setLine($faker->streetAddress());
        $address->setZipCode((int) $faker->postcode());
        $address->setCity($faker->city());

        $this->addReference(self::ADDRESS_REFERENCE . 'admin', $address);

        $manager->persist($address);

        for ($i = 0; $i < 32; $i++) {
            $address = new Address();
            $address->setLine($faker->streetAddress());
            $address->setZipCode($faker->postcode());
            $address->setCity($faker->city());

            $this->addReference(self::ADDRESS_REFERENCE . $i, $address);

            $manager->persist($address);
        }

        $manager->flush();
    }
}
