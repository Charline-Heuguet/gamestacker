<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Forum;
use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Category;
use App\Entity\Platform;
use App\Entity\Announcement;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(ArticleCrudController::class)->generateUrl());

    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Dashboard GameStacker');
            
    }

    public function configureCrud(): Crud
    {
        return parent::configureCrud()
            ->showEntityActionsInlined();
    }


    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::section("Users");
        yield MenuItem::linkToCrud('Users', 'fa fa-user', User::class);

        yield MenuItem::section("Team Play");
        yield MenuItem::linkToCrud('Announcements', 'fas fa-bullhorn', Announcement::class);

        yield MenuItem::section("Forum");
        yield MenuItem::linkToCrud('Forums', 'fas fa-hands-helping', Forum::class);
        yield MenuItem::linkToCrud('Forums Comments', 'fas fa-comments', Comment::class);


        yield MenuItem::section("Blog");
        yield MenuItem::linkToCrud('Articles', 'fab fa-blogger-b', Article::class);
        yield MenuItem::linkToCrud('Articles Comments', 'fas fa-comments', Comment::class);

        yield MenuItem::section("Labels");
        yield MenuItem::linkToCrud("Categories", 'fa fa-tags', Category::class);
        yield MenuItem::linkToCrud("Platforms", 'fas fa-gamepad', Platform::class);
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }
}
