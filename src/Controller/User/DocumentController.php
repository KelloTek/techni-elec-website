<?php

namespace App\Controller\User;

use App\Repository\DocumentRepository;
use App\Service\FileService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/profile/document')]
final class DocumentController extends AbstractController
{
    public function __construct(
        private DocumentRepository $documentRepository,
        private TranslatorInterface $translator,
        private FileService $fileService,
    ) {}

    #[Route('s/{page}', name: 'app_user_documents')]
    public function index(Request $request, int $page = 1): Response
    {
        $offset = (($page - 1) * 10);

        $type = trim($request->query->get('type', ''));
        $search = trim($request->query->get('search', ''));
        $date = trim($request->query->get('date', ''));

        $user = $this->getUser();
        $documents = $this->documentRepository->search($type, $search, $date, 10, $offset, $user);

        return $this->render('user/document/index.html.twig', [
            'root' => 'app_user_documents',
            'type' => $type,
            'search' => $search,
            'date' => $date,
            'page' => $page,
            'documentCount' => $documents['count'],
            'documents' => $documents['results'],
            'optionsType' => [
                ['value' => 'id', 'label' => $this->translator->trans('filter.options.type.id', [], 'forms')],
                ['value' => 'name', 'label' => $this->translator->trans('filter.options.type.name', [], 'forms')],
                ['value' => 'category', 'label' => $this->translator->trans('filter.options.type.category', [], 'forms')],
                ['value' => 'file', 'label' => $this->translator->trans('filter.options.type.file', [], 'forms')],
            ],
        ]);
    }

    #[Route('/view/{document}', name: 'app_user_document')]
    public function show(int $document): Response
    {
        $user = $this->getUser();
        $document = $this->documentRepository->findOneBy(['id' => $document]);

        return $this->fileService->viewFile($user->getId(), $document->getFile()->getName());
    }
}
