<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Post;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{

    private CategoryRepository $repo;
    public function __construct(CategoryRepository $repo)
    {
        $this->repo = $repo;
    }

    #[Route('/category', name: 'cat.index')]
    public function index(): Response
    {
        $cats = $this->repo->findAll();

        return $this->render('category/index.html.twig', [ "cats" => $cats ]);
    }

    #[Route('/category/{id}', name: 'cat.list', methods: ['GET'])]
        public function list($id): Response
    {
        $category = $this->repo->find($id); 
        $posts = $category->getPosts();

        return $this->render('category/list.html.twig', [ "category" => $category, "posts" => $posts ]);
    }

     #[Route('/category/create', name: 'cat.create', methods: ['POST', 'GET'])]
    public function create(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repo->add($category, true);
            return $this->redirectToRoute('cat.index');
        }

        return $this->renderForm('category/create.html.twig', ["form" => $form]);
    }

    #[Route('/category/delete/{id}', name: 'cat.delete')]
    public function delete(int $id): Response
    {
        $category = $this->repo
            ->find($id);
        $this->repo->remove($category, true);
        
        return $this->redirectToRoute('cat.index');
    }
}
