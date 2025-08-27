<?php

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    public const USER_REFERENCE = 'user_';

    public function __construct(private UserPasswordHasherInterface $passwordHasher) {}

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $admin = new User();
        $admin->setEmail('admin@dev.com');
        $admin->setName('Admin');
        $admin->setPhone($faker->unique()->phoneNumber());
        $admin->setAddress($this->getReference(AddressFixtures::ADDRESS_REFERENCE . 'admin', Address::class));
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setIsVerified(true);
        $admin->setCreatedAt();

        $password = $this->passwordHasher->hashPassword($admin, 'admin');
        $admin->setPassword($password);

        $this->addReference(self::USER_REFERENCE . 'admin', $admin);

        $manager->persist($admin);

        for ($i = 0; $i < 32; $i++) {
            $user = new User();
            $user->setEmail($faker->unique()->safeEmail());
            $user->setName($faker->unique()->name());
            $user->setPhone($faker->unique()->phoneNumber());
            $user->setAddress($this->getReference(AddressFixtures::ADDRESS_REFERENCE . $i, Address::class));
            $user->setRoles(['ROLE_USER']);
            $user->setIsVerified(false);
            $user->setCreatedAt();

            $password = $this->passwordHasher->hashPassword($user, 'password');
            $user->setPassword($password);

            $this->addReference(self::USER_REFERENCE . $i, $user);

            $manager->persist($user);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            AddressFixtures::class,
        ];
    }
}
