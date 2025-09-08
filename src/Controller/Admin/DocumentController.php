<?php

namespace App\Controller\Admin;

use App\Entity\Document;
use App\Entity\DocumentCategory;
use App\Entity\File;
use App\Entity\User;
use App\Form\DocumentCategoryDeleteType;
use App\Form\DocumentCategoryType;
use App\Form\DocumentDeleteType;
use App\Form\DocumentType;
use App\Repository\DocumentCategoryRepository;
use App\Repository\DocumentRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/admin/document')]
final class DocumentController extends AbstractController
{
    public function __construct(
        private DocumentRepository $documentRepository,
        private DocumentCategoryRepository $documentCategoryRepository,
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
    public function newDocument(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $document = new Document();
        $form = $this->createForm(DocumentType::class, $document);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /**  @var UploadedFile $uploadedFile */
            $uploadedFile = $form->get('file')->get('upload')->getData();

            if ($uploadedFile) {
                $user = $this->getUser();

                $uploadDir = $this->getParameter('private_uploads_dir'). '/' . $user->getId();

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0775, true);
                }

                $safeFilename = $slugger->slug(pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME));
                $newFilename = $safeFilename . '-' .uniqid() . '.' . $uploadedFile->guessExtension();

                $size = $uploadedFile->getSize();
                $uploadedFile->move($uploadDir, $newFilename);

                $file = new File();
                $file->setOriginalName($uploadedFile->getClientOriginalName());
                $file->setName($newFilename);
                $file->setType($uploadedFile->getClientMimeType());
                $file->setSize($size);
                $file->setPath($uploadedFile->getRealPath());

                $document->setFile($file);

                $em->persist($file);
                $em->persist($document);
                $em->flush();

                $this->addFlash('success', $this->translator->trans('document.new.success', ['%label%' => $document->getName()], 'flashes'));
                return $this->redirectToRoute('app_admin_documents');
            }
        }

        return $this->render('admin/document/new_document.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{document}', name: 'app_admin_document_delete')]
    public function deleteDocument(Document $document, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(DocumentDeleteType::class, $document);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('name')->getData() === $document->getName()) {
            $em->remove($document);
            $em->flush();

            $this->addFlash('success', $this->translator->trans('document.delete.success', ['%label%' => $document->getName()], 'flashes'));
            return $this->redirectToRoute('app_admin_documents');
        }

        return $this->render('admin/document/delete_document.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/view/{user}/{document}', name: 'app_admin_document')]
    public function viewOneByUser(int $user, int $document, UserRepository $userRepository, DocumentRepository $documentRepository): Response
    {
        $user = $userRepository->findOneBy(['id' => $user]);
        $document = $documentRepository->findOneBy(['id' => $document]);

        if ($this->getUser() !== $user && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('You do not have permission to access this file.');
        }

        $filePath = $this->getParameter('private_uploads_dir') . '/' . $user->getId() . '/' . $document->getFile()->getName();

        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('The file does not exist');
        }

        return $this->file($filePath, $document->getName(), ResponseHeaderBag::DISPOSITION_INLINE);
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
    public function newCategory(Request $request, EntityManagerInterface $em): Response
    {
        $category = new DocumentCategory();

        $form = $this->createForm(DocumentCategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em->flush();

            $this->addFlash('success', $this->translator->trans('document_category.new.success', ['%label%' => $category->getLabel()], 'flashes'));
            return $this->redirectToRoute('app_admin_document_categories');
        }

        return $this->render('admin/document/new_category.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/category/edit/{category}', name: 'app_admin_document_category_edit')]
    public function editCategory(DocumentCategory $category, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(DocumentCategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em->flush();

            $this->addFlash('success', $this->translator->trans('document_category.edit.success', ['%label%' => $category->getLabel()], 'flashes'));
            return $this->redirectToRoute('app_admin_document_categories');
        }

        return $this->render('admin/document/edit_category.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/category/delete/{category}', name: 'app_admin_document_category_delete')]
    public function deleteCategory(DocumentCategory $category, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(DocumentCategoryDeleteType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('label')->getData() === $category->getLabel()) {
            $em->remove($category);
            $em->flush();

            $this->addFlash('success', $this->translator->trans('document_category.delete.success', ['%label%' => $category->getLabel()], 'flashes'));
            return $this->redirectToRoute('app_admin_document_categories');
        }

        return $this->render('admin/document/delete_category.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
