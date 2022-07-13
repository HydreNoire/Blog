<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\DBAL\Types\DateImmutableType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    private PostRepository $repo;
    public function __construct(PostRepository $repo)
    {
        $this->repo = $repo;
    }

    #[Route('/', name: 'post_home')]
    public function index(): Response
    {
        $posts = $this->repo->findAll();
        return $this->render('post/index.html.twig', [ 'posts' => $posts ]);
    }
    
    #[Route('/post/details/{id}', name: 'post.show')]
    public function show(int $id): Response
    {
        $post = $this->repo
        ->find($id);

        return $this->render('post/show.html.twig', [ "post" => $post ]);
    }

    #[Route('/post/create', name: 'post_create', methods: ['POST', 'GET'])]
    public function create(Request $request): Response
    {
        $post = new Post();

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setCreatedAt(new \DateTimeImmutable);
            $this->repo->add($post, true);
            return $this->redirectToRoute('post_home');
        }

        return $this->renderForm('post/create.html.twig', ["form" => $form]);
    }
}
