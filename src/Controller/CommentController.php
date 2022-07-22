<?php

namespace App\Controller;

use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

    #[Route('/post/details/{postId}/comment/delete/{commentId}', name: 'comment_delete')]
    public function delete(int $postId, int $commentId): Response
    {        
        $findComment = $this->repo->find($commentId);
        $user = $this->getUser();

        if ($user == $findComment->getUser() || $this->getUser()->getRoles('ROLE_EDITOR')) {
           $this->repo->remove($findComment, true);
        }

        return $this->redirectToRoute('post.show', ['id' => $postId]);
    }
}
