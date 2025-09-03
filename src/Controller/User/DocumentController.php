<?php

namespace App\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/profile/document')]
final class DocumentController extends AbstractController
{
    #[Route('s', name: 'app_user_document')]
    public function index(): Response
    {
        return $this->render('user/document/index.html.twig', [
            'controller_name' => 'DocumentController',
        ]);
    }

    #[Route('/{document}', name: 'app_user_document_show')]
    public function show(string $document): Response
    {
        $user = $this->getUser();

        $filePath = $this->getParameter('kernel.project_dir') . "/private/uploads/" . $user->getUserIdentifier() . "/$document";

        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('The file does not exist');
        }

        return $this->file($filePath, $document, ResponseHeaderBag::DISPOSITION_INLINE);
    }
}
