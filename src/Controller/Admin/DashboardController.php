<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Post;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    public function __construct(private AdminUrlGenerator $adminUrlGenerator)
    {
        
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $url = $this->adminUrlGenerator
        ->setController(UserCrudController::class)
        ->generateUrl();

        return $this->redirect($url);
        // return $this->render('admin/index.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Blog Dashboard');
    }

    public function configureMenuItems(): iterable
    {
        // yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);

            yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

            yield MenuItem::section('Blog');
            yield MenuItem::subMenu('Categories', 'fa fa-tags')->setSubItems([
                MenuItem::linkToCrud('All Categories', 'fa fa-file-text', Category::class)->setAction(Crud::PAGE_INDEX),
                MenuItem::linkToCrud('Add Category', 'fas fa-plus', Category::class)->setAction(Crud::PAGE_NEW)
            ]);
            yield MenuItem::subMenu('Posts', 'fa fa-file-text')->setSubItems([
                MenuItem::linkToCrud('All Post', 'fa fa-file-text', Post::class)->setAction(Crud::PAGE_INDEX),
                MenuItem::linkToCrud('Add Post', 'fa fa-file-text', Post::class)->setAction(Crud::PAGE_NEW)
            ]);

            yield MenuItem::section('Users');
            yield MenuItem::subMenu('Manage Users', "fa-solid fa-gear")->setSubItems([
                MenuItem::linkToCrud('Users', 'fa fa-user', User::class)->setAction(Crud::PAGE_INDEX),
            ]);;

    }
}
