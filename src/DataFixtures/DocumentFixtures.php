<?php

namespace App\DataFixtures;

use App\Entity\Document;
use App\Entity\DocumentCategory;
use App\Entity\File;
use App\Entity\Request;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class DocumentFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $document = new Document();
        $document->setName($faker->word());
        $document->setCategory($this->getReference(DocumentCategoryFixtures::DOCUMENT_CATEGORY_REFERENCE . rand(0, 9), DocumentCategory::class));
        $document->setUser($this->getReference(UserFixtures::USER_REFERENCE . 'admin', User::class));
        $document->setFile($this->getReference(FileFixtures::PDF_REFERENCE . rand(0, 2), File::class));
        $document->setRequest($this->getReference(RequestFixtures::REQUEST_REFERENCE . rand(0, 7), Request::class));
        $document->setCreatedAt();

        $manager->persist($document);

        for ($i = 0; $i < 10; $i++) {
            $document = new Document();
            $document->setName($faker->word());
            $document->setCategory($this->getReference(DocumentCategoryFixtures::DOCUMENT_CATEGORY_REFERENCE . $i, DocumentCategory::class));
            $document->setUser($this->getReference(UserFixtures::USER_REFERENCE . $i, User::class));
            $document->setFile($this->getReference(FileFixtures::PDF_REFERENCE . rand(0, 2), File::class));
            $document->setRequest($this->getReference(RequestFixtures::REQUEST_REFERENCE . rand(0, 7), Request::class));
            $document->setCreatedAt();

            $manager->persist($document);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            DocumentCategoryFixtures::class,
            FileFixtures::class,
            UserFixtures::class,
            RequestFixtures::class,
        ];
    }
}
