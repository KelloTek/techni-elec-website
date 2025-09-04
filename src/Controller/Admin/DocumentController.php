<?php

namespace App\Controller\Admin;

use App\Repository\DocumentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/admin/document')]
final class DocumentController extends AbstractController
{
    public function __construct(
        private DocumentRepository $documentRepository,
        private TranslatorInterface $translator
    ) {}

    #[Route('s/{page}', name: 'app_admin_documents')]
    public function index(Request $request, int $page = 1): Response
    {
        $offset = (($page - 1) * 10);

        $type = trim($request->query->get('type', ''));
        $search = trim($request->query->get('search', ''));
        $date = trim($request->query->get('date', ''));

        $documents = $this->documentRepository->search($type, $search, $date, 10, $offset);

        return $this->render('admin/document/index.html.twig', [
            'root' => 'app_admin_documents',
            'type' => $type,
            'search' => $search,
            'date' => $date,
            'page' => $page,
            'documentCount' => $documents['count'],
            'documents' => $documents['results'],
            'optionsType' => [
                ['value' => 'all', 'label' => $this->translator->trans('filter.options.type.all', [], 'forms')],
                ['value' => 'id', 'label' => $this->translator->trans('filter.options.type.id', [], 'forms')],
                ['value' => 'name', 'label' => $this->translator->trans('filter.options.type.name', [], 'forms')],
                ['value' => 'email', 'label' => $this->translator->trans('filter.options.type.email', [], 'forms')],
                ['value' => 'phone', 'label' => $this->translator->trans('filter.options.type.phone', [], 'forms')],
                ['value' => 'address', 'label' => $this->translator->trans('filter.options.type.address', [], 'forms')],
            ],
        ]);
    }

    #[Route('/{user}/{document}', name: 'app_admin_document')]
    public function show(int $user, string $document): Response
    {
        $filePath = $this->getParameter('kernel.project_dir') . "/private/uploads/$user/$document";

        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('The file does not exist');
        }

        return $this->file($filePath, $document, ResponseHeaderBag::DISPOSITION_INLINE);
    }
}
