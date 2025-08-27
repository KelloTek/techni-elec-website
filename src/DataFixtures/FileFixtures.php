<?php

namespace App\DataFixtures;

use App\Entity\File;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class FileFixtures extends Fixture
{
    public const IMAGE_REFERENCE = 'image_';
    public const PDF_REFERENCE = 'pdf_';

    public function load(ObjectManager $manager): void
    {
        $uploadDir = 'public/uploads';
        $privateUploadDir = 'private/uploads/admin';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (!is_dir($privateUploadDir)) {
            mkdir($privateUploadDir, 0777, true);
        }

        for ($i = 0; $i < 5; $i++) {
            $filename = 'image_' . uniqid() . '.jpg';
            $filepath = $uploadDir . '/' . $filename;

            $image = imagecreatetruecolor(640, 480);
            $color = imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255));
            imagefill($image, 0, 0, $color);
            imagejpeg($image, $filepath);
            imagedestroy($image);

            $file = new File();
            $file->setName($filename);
            $file->setPath($filepath);
            $file->setSize(filesize($filepath));
            $file->setType('jpg');

            $this->addReference(self::IMAGE_REFERENCE. $i, $file);

            $manager->persist($file);
        }

        for ($i = 0; $i < 3; $i++) {
            $filename = 'document_' . uniqid() . '.pdf';
            $filepath = $privateUploadDir . '/' . $filename;

            $pdfContent = "%PDF-1.4\n1 0 obj\n<< /Type /Catalog >>\nendobj\ntrailer\n<< >>\n%%EOF";
            file_put_contents($filepath, $pdfContent);

            $file = new File();
            $file->setName($filename);
            $file->setPath($filepath);
            $file->setSize(filesize($filepath));
            $file->setType('pdf');

            $this->addReference(self::PDF_REFERENCE . $i, $file);

            $manager->persist($file);
        }

        $manager->flush();
    }
}
