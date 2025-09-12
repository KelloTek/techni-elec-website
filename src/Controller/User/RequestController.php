<?php

namespace App\Controller\User;

use App\Entity\Discussion;
use App\Entity\Request as RequestEntity;
use App\Form\DiscussionType;
use App\Form\RequestType;
use App\Repository\RequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/profile/request')]
final class RequestController extends AbstractController
{
    public function __construct(
        private RequestRepository $requestRepository,
        private EntityManagerInterface $entityManager,
        private TranslatorInterface $translator
    ) {}

    #[Route('s/{page}', name: 'app_user_requests')]
    public function index(Request $request, int $page = 1): Response
    {
        $offset = (($page - 1) * 10);

        $type = trim($request->query->get('type', ''));
        $search = trim($request->query->get('search', ''));
        $date = trim($request->query->get('date', ''));

        $user = $this->getUser();
        $requests = $this->requestRepository->search($type, $search, $date, 10, $offset, $user);

        return $this->render('user/request/index.html.twig', [
            'root' => 'app_user_requests',
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

    #[Route('/view/{requestEntity}', name: 'app_user_request')]
    public function show(RequestEntity $requestEntity, Request $request): Response
    {
        if ($requestEntity->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You do not have access to this request.');
        }

        $message = new Discussion();
        $form = $this->createForm(DiscussionType::class, $message);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message->setUser($this->getUser());
            $message->setRequest($requestEntity);

            $this->entityManager->persist($message);
            $this->entityManager->flush();
        }

        return $this->render('user/request/show.html.twig', [
            'request' => $requestEntity,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/new', name: 'app_user_request_new')]
    public function new(Request $request): Response
    {
        $requests = new RequestEntity();
        $form = $this->createForm(RequestType::class, $requests);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $requests->setUser($this->getUser());

            $this->entityManager->persist($requests);
            $this->entityManager->flush();

            $this->addFlash('success', $this->translator->trans('request.new.success', ['%id%' => $requests->getId()], 'flashes'));
            return $this->redirectToRoute('app_user_requests');
        }

        return $this->render('user/request/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
