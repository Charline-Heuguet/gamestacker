<?php

namespace App\Controller\Admin;

use App\Entity\ReportComment;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ReportCommentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ReportComment::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('subject.name', 'Category'),  // Nom de la catégorie
            TextField::new('user.pseudo', 'User'),       // Nom de l'utilisateur
            TextField::new('comment.content', 'Comment Content') // Contenu du commentaire
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL); // Ajoute l'action de détail à la page index
    }
}
