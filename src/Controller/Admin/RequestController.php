<?php

namespace App\Controller\Admin;

use App\Entity\Request as RequestEntity;
use App\Repository\RequestRepository;
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

    #[Route('/view/{request}', name: 'app_admin_request')]
    public function show(RequestEntity $request): Response
    {
        return $this->render('admin/request/show.html.twig', [
            'request' => $request,
        ]);
    }
}
