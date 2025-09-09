<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\DeleteType;
use App\Repository\DocumentRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/admin/user')]
final class UserController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private DocumentRepository $documentRepository,
        private TranslatorInterface $translator
    ) {}

    #[Route('s/{page}', name: 'app_admin_users')]
    public function index(Request $request, int $page = 1): Response
    {
        $offset = (($page - 1) * 10);

        $type = trim($request->query->get('type', ''));
        $search = trim($request->query->get('search', ''));
        $date = trim($request->query->get('date', ''));

        $users = $this->userRepository->search($type, $search, $date, 10, $offset);

        return $this->render('admin/user/index.html.twig', [
            'root' => 'app_admin_users',
            'type' => $type,
            'search' => $search,
            'date' => $date,
            'page' => $page,
            'userCount' => $users['count'],
            'users' => $users['results'],
            'optionsType' => [
                ['value' => 'id', 'label' => $this->translator->trans('filter.options.type.id', [], 'forms')],
                ['value' => 'name', 'label' => $this->translator->trans('filter.options.type.name', [], 'forms')],
                ['value' => 'email', 'label' => $this->translator->trans('filter.options.type.email', [], 'forms')],
                ['value' => 'phone', 'label' => $this->translator->trans('filter.options.type.phone', [], 'forms')],
                ['value' => 'address', 'label' => $this->translator->trans('filter.options.type.address', [], 'forms')],
            ],
        ]);
    }

    #[Route('/view/{id}/{page}', name: 'app_admin_user')]
    public function show(Request $request, TranslatorInterface $translator, int $id, int $page = 1): Response
    {
        $offset = (($page - 1) * 10);

        $type = trim($request->query->get('type', ''));
        $search = trim($request->query->get('search', ''));
        $date = trim($request->query->get('date', ''));

        $user = $this->userRepository->find($id);

        $documents = $this->documentRepository->searchByOneUser($user->getId(), $type, $search, $date, 10, $offset);

        return $this->render('admin/user/show.html.twig', [
            'root' => 'app_admin_user',
            'params' => ['id' => $id],
            'type' => $type,
            'search' => $search,
            'date' => $date,
            'page' => $page,
            'user' => $user,
            'documentCount' => $documents['count'],
            'documents' => $documents['results'],
            'optionsType' => [
                ['value' => 'id', 'label' => $translator->trans('filter.options.type.id', [], 'forms')],
                ['value' => 'name', 'label' => $translator->trans('filter.options.type.name', [], 'forms')],
                ['value' => 'category', 'label' => $translator->trans('filter.options.type.category', [], 'forms')],
                ['value' => 'file', 'label' => $translator->trans('filter.options.type.file', [], 'forms')],
            ],
        ]);
    }

    #[Route('/delete/{user}', name: 'app_admin_user_delete')]
    public function delete(Request $request, User $user, EntityManagerInterface $em): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('You do not have permission to delete this user.');
        }

        $form = $this->createForm(DeleteType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('name')->getData() === $user->getName()) {
            $this->userRepository->delete($user);

            $this->addFlash('success', $this->translator->trans('user.delete.success', ['%username%' => $user->getName()], 'flashes'));
            return $this->redirectToRoute('app_admin_users');
        }

        return $this->render('admin/user/delete.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
