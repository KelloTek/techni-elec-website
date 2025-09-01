<?php

namespace App\Controller\Admin;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
final class UserController extends AbstractController
{
    public function __construct(private UserRepository $userRepository) {}

    // Show all users with pagination
    #[Route('/users/{page}', name: 'app_admin_users')]
    public function index(Request $request, int $page = 1): Response
    {
        $offset = (($page - 1) * 10);

        $search = trim($request->query->get('search', ''));
        $date = trim($request->query->get('date', ''));

        $users = $this->userRepository->search($search, $date, 10, $offset);

        $userCount = $this->userRepository->getSearchCount($search, $date);

        return $this->render('admin/user/index.html.twig', [
            'root' => 'app_admin_users',
            'search' => $search,
            'date' => $date,
            'page' => $page,
            'userCount' => $userCount,
            'users' => $users,
        ]);
    }

    // Show one user
    #[Route('/user/{id}', name: 'app_admin_user')]
    public function show(int $id): Response
    {
        $user = $this->userRepository->find($id);

        return $this->render('admin/user/show.html.twig', [
            'user' => $user,
        ]);
    }
}
