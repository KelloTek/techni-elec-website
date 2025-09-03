<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/document')]
final class DocumentController extends AbstractController
{
    #[Route('s', name: 'app_admin_document')]
    public function index(): Response
    {
        return $this->render('admin/document/index.html.twig', [
            'controller_name' => 'DocumentController',
        ]);
    }

    #[Route('/{user}/{document}', name: 'app_admin_document_show')]
    public function show(int $user, string $document): Response
    {
        $filePath = $this->getParameter('kernel.project_dir') . "/private/uploads/$user/$document";

        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('The file does not exist');
        }

        return $this->file($filePath, $document, ResponseHeaderBag::DISPOSITION_INLINE);
    }
}
