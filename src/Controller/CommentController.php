<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    private CommentRepository $repo;
    public function __construct(
        CommentRepository $repo,
    ) {
        $this->repo = $repo;
    }

    #[Route('comment/delete/{commentId}', name: 'comment_delete')]
    public function delete(int $commentId): Response
    {        
        $findComment = $this->repo->find($commentId);
        $user = $this->getUser();

        if ($user == $findComment->getUser() || $this->getUser()->getRoles('ROLE_EDITOR')) {
           $this->repo->remove($findComment, true);
        }

        return $this->redirectToRoute('post.show', ['id' => $findComment->getPost()->getId()]);
    }

    #[Route('/post/details/comment/{postId}', name: 'comment_create', methods: ["POST"])]
    public function create(Request $request, int $postId, PostRepository $repoPost): Response
    {        
        $post = $repoPost->find($postId);
        $user = $this->getUser();
        $comBody = $request->toArray();

        $com = new Comment;
        $com->setCreatedAt(new \DateTimeImmutable())->setUser($user)->setPost($post)->setContent($comBody['content']);
        $this->repo->add($com, true);

        $comments = $post->getComments();
        $allComments = [];

        foreach($comments as $comment) {
            $allComments = [
                "id" => $comment->getId(),
                "currentUserId" => $user->getId(),
                "currentUserUsername" => $comment->getUser()->getUsername(),
                "user" => $user->getUsername(),
                "userRole" => $user->getRoles(),
                "content" => $comment->getContent(),
                "createAt" => $comment->getCreatedAt()->format("d/m/Y H:i"),
                "numberOfComm" => $this->repo->count(['post' => $post])
            ];
        }

        return $this->json($allComments);
    }
    
}
