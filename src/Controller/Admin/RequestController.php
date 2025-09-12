<?php

namespace App\Controller\Admin;

use App\Entity\Discussion;
use App\Entity\Request as RequestEntity;
use App\Entity\RequestCategory;
use App\Form\DeleteType;
use App\Form\DiscussionType;
use App\Form\RequestCategoryType;
use App\Repository\RequestCategoryRepository;
use App\Repository\RequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/admin/request')]
final class RequestController extends AbstractController
{
    public function __construct(
        private RequestRepository $requestRepository,
        private RequestCategoryRepository $requestCategoryRepository,
        private EntityManagerInterface $entityManager,
        private TranslatorInterface $translator
    ) {}

    #[Route('s', name: 'app_admin_requests')]
    public function index(Request $request, int $page = 1): Response
    {
        $offset = (($page - 1) * 10);

        $type = trim($request->query->get('type', ''));
        $search = trim($request->query->get('search', ''));
        $date = trim($request->query->get('date', ''));

        $requests = $this->requestRepository->search($type, $search, $date, 10, $offset);

        return $this->render('admin/request/index.html.twig', [
            'root' => 'app_admin_requests',
            'type' => $type,
            'search' => $search,
            'date' => $date,
            'page' => $page,
            'requestCount' => $requests['count'],
            'requests' => $requests['results'],
            'optionsType' => [
                ['value' => 'id', 'label' => $this->translator->trans('filter.options.type.id', [], 'forms')],
                ['value' => 'title', 'label' => $this->translator->trans('filter.options.type.title', [], 'forms')],
            ],
        ]);
    }

    #[Route('/view/{requestEntity}', name: 'app_admin_request')]
    public function show(RequestEntity $requestEntity, Request $request): Response
    {
        $message = new Discussion();

        $form = $this->createForm(DiscussionType::class, $message);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message->setUser($this->getUser());
            $message->setRequest($requestEntity);

            $this->entityManager->persist($message);
            $this->entityManager->flush();
        }

        return $this->render('admin/request/show.html.twig', [
            'request' => $requestEntity,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{request}', name: 'app_admin_request_delete')]
    public function delete(RequestEntity $requestEntity, Request $request): Response
    {
        $form = $this->createForm(DeleteType::class, $requestEntity);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('name')->getData() === $requestEntity->getTitle()) {
            $this->entityManager->remove($requestEntity);
            $this->entityManager->flush();

            $this->addFlash('success', $this->translator->trans('request.delete.success', ['%id%' => $requestEntity->getId()], 'flashes'));
            return $this->redirectToRoute('app_admin_requests');
        }

        return $this->render('admin/request/delete.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/categories/{page}', name: 'app_admin_request_categories')]
    public function categories(Request $request, int $page = 1): Response
    {
        $offset = (($page - 1) * 10);

        $type = trim($request->query->get('type', ''));
        $search = trim($request->query->get('search', ''));
        $date = trim($request->query->get('date', ''));

        $categories = $this->requestCategoryRepository->search($type, $search, $date, 10, $offset);

        return $this->render('admin/request/categories/index.html.twig', [
            'root' => 'app_admin_request_categories',
            'type' => $type,
            'search' => $search,
            'date' => $date,
            'page' => $page,
            'count' => $categories['count'],
            'categories' => $categories['results'],
            'optionsType' => [
                ['value' => 'id', 'label' => $this->translator->trans('filter.options.type.id', [], 'forms')],
                ['value' => 'label', 'label' => $this->translator->trans('filter.options.type.label', [], 'forms')],
            ],
        ]);
    }

    #[Route('/category/new', name: 'app_admin_request_category_new')]
    public function newCategory(Request $request): Response
    {
        $category = new RequestCategory();

        $form = $this->createForm(RequestCategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($category);
            $this->entityManager->flush();

            $this->addFlash('success', $this->translator->trans('request_category.new.success', ['%name%' => $category->getLabel()], 'flashes'));
            return $this->redirectToRoute('app_admin_request_categories');
        }

        return $this->render('admin/request/categories/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/category/edit/{category}', name: 'app_admin_request_category_edit')]
    public function editCategory(RequestCategory $category, Request $request): Response
    {
        $form = $this->createForm(RequestCategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($category);
            $this->entityManager->flush();

            $this->addFlash('success', $this->translator->trans('request_category.edit.success', ['%name%' => $category->getLabel()], 'flashes'));
            return $this->redirectToRoute('app_admin_request_categories');
        }

        return $this->render('admin/request/categories/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/category/delete/{category}', name: 'app_admin_request_category_delete')]
    public function deleteCategory(RequestCategory $category, Request $request): Response
    {
        $form = $this->createForm(DeleteType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('name')->getData() === $category->getLabel()) {
            $this->entityManager->remove($category);
            $this->entityManager->flush();

            $this->addFlash('success', $this->translator->trans('request_category.delete.success', ['%name%' => $category->getLabel()], 'flashes'));
            return $this->redirectToRoute('app_admin_request_categories');
        }

        return $this->render('admin/request/categories/delete.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
