<?php

namespace App\Controller\User;

use App\Form\SettingsType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;


#[Route('/profile')]
final class HomeController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TranslatorInterface $translator,
    ) {}

    #[Route('', name: 'app_user_home')]
    public function index(): Response
    {
        return $this->render('user/home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/settings', name: 'app_user_settings')]
    public function settings(Request $request, UserRepository $userRepository): Response
    {
        $user = $userRepository->findOneBy(['email' => $this->getUser()->getEmail()]);
        $form = $this->createForm(SettingsType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->addFlash('success', $this->translator->trans('settings.save.success', [], 'flashes'));
            return $this->redirectToRoute('app_user_settings');
        }

        return $this->render('user/home/settings.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
