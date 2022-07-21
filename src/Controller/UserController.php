<?php

namespace App\Controller;

use App\Form\EditProfileType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{

    private UserRepository $repo;
    public function __construct(UserRepository $repo)
    {
        $this-> repo = $repo;
    }

    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig');
    }

    #[Route('/user/edit', name: 'app_edit')]
    public function edit(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(EditProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();

            $this->addFlash('message', "You're profile has been updated!");
            return $this->redirectToRoute('app_user');
        }

        return $this->renderForm('user/edit.html.twig', ["form" => $form]);
    }

    #[Route('/user/edit/password', name: 'app_edit_password')]
    public function editPassword(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        if ($request->isMethod("POST")) {
            /**
             * @var User
             */
            $user = $this->getUser();

            if ($request->request->get('password1') == $request->request->get('password2')) {
                $user->setPassword(
                $hasher->hashPassword(
                    $user,
                    $request->request->get('password1')
                )
            );
            $em->persist($user);
            $em->flush();

                $this->addFlash('message', "You're password has been updated!");
                return $this->redirectToRoute('app_user');
            } else {
                $this->addFlash('error', "Password aren't the same, try again.");
            }
        }

        return $this->renderForm('user/editpassword.html.twig');
    }

    #[Route('/user/delete', name: 'user_delete')]
    public function delete()
    {
        $user = $this->getUser();
        $this->repo->remove($user, true);

        session_destroy();

        return $this->redirectToRoute('post_home');
    }
}
