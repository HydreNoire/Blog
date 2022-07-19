<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;

class PostCrudController extends AbstractCrudController
{
    public const ACTION_DUPLICATE = 'duplicate';
    
    public static function getEntityFqcn(): string
    {
        return Post::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $duplicate = Action::new(self::ACTION_DUPLICATE)
        ->linkToCrudAction('duplicatePost');
        return $actions->add(Crud::PAGE_EDIT, $duplicate);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('title'),
            AssociationField::new('category'),
            TextField::new('description'),
            TextEditorField::new('content'),
            DateTimeField::new('created_at')->hideOnForm()
        ];
    }

    public function duplicatePost(AdminContext $context, AdminUrlGenerator $adminUrlGenerator, EntityManagerInterface $em): Response
    {
        /** @var Post $post **/
        $post = $context->getEntity()->getInstance();

        $duplicatedPost = clone $post;
        parent::persistEntity($em, $duplicatedPost);

        $url = $adminUrlGenerator->setController(self::class)
                                ->setAction(Action::DETAIL)
                                ->setEntityId($duplicatedPost->getId())
                                ->generateUrl();

        return $this->redirect($url);
    }
}
