<?php

namespace App\Controller;

use App\Entity\Conference;
use App\Repository\CommentRepository;
use App\Repository\ConferenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class ConferenceController extends AbstractController
{
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @Route("/", name="homepage")
     */
    public function index(): Response
    {
        return new Response($this->twig->render('conference/index.html.twig'));
    }

    /**
     * @Route("/conference/{id}", name="conference")
     */
    public function view(
        Request $request,
        Conference $conference,
        CommentRepository $commentRepository
    ): Response
    {
        $offset = max(0, $request->query->get('offset', 0));
        $paginator = $commentRepository->getPaginator($conference, $offset);

        return new Response($this->twig->render('conference/view.html.twig', [
            'conference' => $conference,
            'comments' => $paginator,
            'previous' => $offset - CommentRepository::COMMENTS_PER_PAGE,
            'next' => min(count($paginator), $offset + CommentRepository::COMMENTS_PER_PAGE),
        ]));
    }
}
