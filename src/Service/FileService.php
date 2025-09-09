<?php

namespace App\Service;

use App\Entity\File;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileService
{
    public function __construct(
        private string $privateUploadsDir,
        private SluggerInterface $slugger,
        private EntityManagerInterface $entityManager
    ) {}

    public function uploadFile(UploadedFile $uploadedFile, int $userId): File
    {
        $uploadDir = $this->privateUploadsDir . '/' . $userId;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        $safeFilename = $this->slugger->slug(pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME));
        $newFilename = $safeFilename . '-' .uniqid() . '.' . $uploadedFile->guessExtension();

        $size = $uploadedFile->getSize();
        $uploadedFile->move($uploadDir, $newFilename);

        $file = new File();
        $file->setOriginalName(pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME));
        $file->setName($newFilename);
        $file->setType($uploadedFile->getClientMimeType());
        $file->setSize($size);
        $file->setPath($uploadDir . '/' . $newFilename);

        $this->entityManager->persist($file);

        return $file;
    }

    public function viewFile(int $userId, string $fileName): BinaryFileResponse
    {
        $filePath = $this->privateUploadsDir . '/' . $userId . '/' . $fileName;

        return new BinaryFileResponse($filePath, 200, [], false, ResponseHeaderBag::DISPOSITION_INLINE);
    }

    public function deleteFile(File $file): void
    {
        if (file_exists($file->getPath())) {
            unlink($file->getPath());
        }

        $this->entityManager->remove($file);
        $this->entityManager->flush();
    }
}
