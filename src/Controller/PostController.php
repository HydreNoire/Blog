<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Like;
use App\Entity\Post;
use App\Form\CommentType;
use App\Form\PostType;
use App\Repository\CommentRepository;
use App\Repository\LikeRepository;
use App\Repository\PostRepository;
use Doctrine\DBAL\Types\DateImmutableType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    private PostRepository $repo;
    private CommentRepository $commentRepo;
    public function __construct(PostRepository $repo, CommentRepository $commentRepo)
    {
        $this->repo = $repo;
        $this->commentRepo = $commentRepo;
    }

    #[Route('/', name: 'post_home')]
    public function index(): Response
    {
        $posts = $this->repo->findAll();
        return $this->render('post/index.html.twig', ['posts' => $posts]);
    }

    #[Route('/post/details/{id}', name: 'post.show')]
    public function show($id, Request $request): Response
    {
        $post = $this->repo->find($id);
        $user = $this->getUser();

        $comment = new Comment();
        $formCom = $this->createForm(CommentType::class, $comment);
        $formCom->handleRequest($request);

        // if ($formCom->isSubmitted() && $formCom->isValid()) {
        //         $comment->setCreatedAt(new \DateTimeImmutable())->setUser($user)->setPost($post);
        //         $this->commentRepo->add($comment, true);
        //         return $this->redirectToRoute('post.show', ['id' => $post->getId()]);
        //     }

        return $this->renderForm('post/show.html.twig', ['post' => $post, "formCom" => $formCom, "comment" => $comment]);
    }

    #[Route('/post/create', name: 'post_create', methods: ['POST', 'GET'])]
    public function create(Request $request): Response
    {
        if ($this->isGranted('ROLE_EDITOR')) {
            $post = new Post();

            $form = $this->createForm(PostType::class, $post);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $post->setCreatedAt(new \DateTimeImmutable());
                $this->repo->add($post, true);
                return $this->redirectToRoute('post_home');
            }

            return $this->renderForm('post/create.html.twig', [
                'form' => $form,
            ]);
        } elseif ($this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('deny_access');
        }
    }

    /**
     * Permet de like et unlike un post
     *
     * @param Post $post
     * @param ObjectManager $manager
     * @param LikeRepository $likerepo
     * @return Response
     */
    #[Route('/post/{id}/like', name: 'post_like')]
    public function like(Post $post, EntityManagerInterface $entitymanager, LikeRepository $likerepo) : Response
    {
        $user = $this->getUser();

        if(!$user) return $this->json([
            'code' => 403,
            'message' => 'You need to be connected to like post'
        ], 403);

        if($post->isLikedByUser($user)) {
            $like = $likerepo->findOneBy([
                'post' => $post,
                'user' => $user
            ]);

            $entitymanager->remove($like);
            $entitymanager->flush();

            return $this->json([
                'code' => 200,
                'message' => 'Like supp',
                'likes' => $likerepo->count(['post' => $post])
            ], 200);
        }

        $like = new Like();
        $like->setPost($post)
            ->setUser($user);
        
        $entitymanager->persist($like);
        $entitymanager->flush();

        return $this->json([
            'code' => 200, 
            'message' => 'Ca marche',
            'likes' => $likerepo->count(['post' => $post])
            ], 200);
    }
}
