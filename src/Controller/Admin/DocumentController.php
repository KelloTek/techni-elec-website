<?php

namespace App\Controller\Admin;

use App\Entity\Document;
use App\Entity\DocumentCategory;
use App\Form\DeleteType;
use App\Form\DocumentCategoryType;
use App\Form\DocumentType;
use App\Repository\DocumentCategoryRepository;
use App\Repository\DocumentRepository;
use App\Repository\UserRepository;
use App\Service\FileService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/admin/document')]
final class DocumentController extends AbstractController
{
    public function __construct(
        private DocumentRepository $documentRepository,
        private DocumentCategoryRepository $documentCategoryRepository,
        private EntityManagerInterface $entityManager,
        private FileService $fileService,
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
                ['value' => 'id', 'label' => $this->translator->trans('filter.options.type.id', [], 'forms')],
                ['value' => 'name', 'label' => $this->translator->trans('filter.options.type.name', [], 'forms')],
                ['value' => 'user', 'label' => $this->translator->trans('filter.options.type.user', [], 'forms')],
                ['value' => 'category', 'label' => $this->translator->trans('filter.options.type.category', [], 'forms')],
                ['value' => 'file', 'label' => $this->translator->trans('filter.options.type.file', [], 'forms')],
            ],
        ]);
    }

    #[Route('/new', name: 'app_admin_document_new')]
    public function newDocument(Request $request): Response
    {
        $document = new Document();
        $form = $this->createForm(DocumentType::class, $document);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /**  @var UploadedFile $uploadedFile */
            $uploadedFile = $form->get('file')->get('upload')->getData();

            if ($uploadedFile) {
                $file = $this->fileService->uploadFile($uploadedFile, $document->getUser()->getId());

                $document->setFile($file);

                $this->entityManager->persist($document);
                $this->entityManager->flush();

                $this->addFlash('success', $this->translator->trans('document.new.success', ['%name%' => $document->getName()], 'flashes'));
                return $this->redirectToRoute('app_admin_documents');
            }
        }

        return $this->render('admin/document/new_document.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{document}', name: 'app_admin_document_delete')]
    public function deleteDocument(Document $document, Request $request): Response
    {
        $form = $this->createForm(DeleteType::class, $document);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('name')->getData() === $document->getName()) {
            $this->entityManager->remove($document);
            $this->entityManager->flush();

            $this->addFlash('success', $this->translator->trans('document.delete.success', ['%name%' => $document->getName()], 'flashes'));
            return $this->redirectToRoute('app_admin_documents');
        }

        return $this->render('admin/document/delete_document.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/view/{user}/{document}', name: 'app_admin_document')]
    public function viewOneByUser(int $user, int $document, UserRepository $userRepository): Response
    {
        $user = $userRepository->findOneBy(['id' => $user]);
        $document = $this->documentRepository->findOneBy(['id' => $document]);

        return $this->fileService->viewFile($user->getId(), $document->getFile()->getName());
    }

    #[Route('/categories/{page}', name: 'app_admin_document_categories')]
    public function categories(Request $request, int $page = 1): Response
    {
        $offset = (($page - 1) * 10);

        $type = trim($request->query->get('type', ''));
        $search = trim($request->query->get('search', ''));
        $date = trim($request->query->get('date', ''));

        $documentCategories = $this->documentCategoryRepository->search($type, $search, $date, 10, $offset);

        return $this->render('admin/document/categories.html.twig', [
            'root' => 'app_admin_document_categories',
            'type' => $type,
            'search' => $search,
            'date' => $date,
            'page' => $page,
            'documentCategoriesCount' => $documentCategories['count'],
            'documentCategories' => $documentCategories['results'],
            'optionsType' => [
                ['value' => 'id', 'label' => $this->translator->trans('filter.options.type.id', [], 'forms')],
                ['value' => 'label', 'label' => $this->translator->trans('filter.options.type.label', [], 'forms')],
            ],
        ]);
    }

    #[Route('/category/new', name: 'app_admin_document_category_new')]
    public function newCategory(Request $request): Response
    {
        $category = new DocumentCategory();

        $form = $this->createForm(DocumentCategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($category);
            $this->entityManager->flush();

            $this->addFlash('success', $this->translator->trans('document_category.new.success', ['%name%' => $category->getLabel()], 'flashes'));
            return $this->redirectToRoute('app_admin_document_categories');
        }

        return $this->render('admin/document/new_category.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/category/edit/{category}', name: 'app_admin_document_category_edit')]
    public function editCategory(DocumentCategory $category, Request $request): Response
    {
        $form = $this->createForm(DocumentCategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($category);
            $this->entityManager->flush();

            $this->addFlash('success', $this->translator->trans('document_category.edit.success', ['%name%' => $category->getLabel()], 'flashes'));
            return $this->redirectToRoute('app_admin_document_categories');
        }

        return $this->render('admin/document/edit_category.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/category/delete/{category}', name: 'app_admin_document_category_delete')]
    public function deleteCategory(DocumentCategory $category, Request $request): Response
    {
        $form = $this->createForm(DeleteType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('label')->getData() === $category->getLabel()) {
            $this->entityManager->remove($category);
            $this->entityManager->flush();

            $this->addFlash('success', $this->translator->trans('document_category.delete.success', ['%name%' => $category->getLabel()], 'flashes'));
            return $this->redirectToRoute('app_admin_document_categories');
        }

        return $this->render('admin/document/delete_category.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
