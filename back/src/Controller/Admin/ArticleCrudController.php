<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Field\VichFileField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ArticleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Article::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title'),
            TextField::new('content'),
            DateTimeField::new('date'),

            // Utilisation de VichFileField pour l'upload de l'image
            VichFileField::new('imageFile', 'Image')
                ->onlyOnForms() // Pour afficher seulement dans les formulaires
                ->setHelp('Attention : taille des images autorisée à 2 Mo maximum.'),

            // Affiche l'image actuelle sur la page d'index et les détails
            ImageField::new('image')
                ->setBasePath('/images/articles')
                ->onlyOnIndex(),
                
            AssociationField::new('category', 'Categories')
                ->formatValue(function ($value, $entity) {
                    return implode(', ', array_map(function ($category) {
                        return $category->getName();
                    }, $entity->getCategory()->toArray()));
                }),

            ArrayField::new('comment', 'Comments')
                ->formatValue(function ($value, $entity) {
                    $comments = array_slice($entity->getComment()->toArray(), -3);
                    return implode('<br>', array_map(function ($comment) {
                        $user = $comment->getUser();
                        $pseudo = $user ? $user->getPseudo() : 'Anonymous';
                        return sprintf('<strong>%s</strong>: %s', $pseudo, $comment->getContent());
                    }, $comments));
                })
                ->onlyOnIndex(),

        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->add(Crud::PAGE_INDEX, Action::DETAIL);
    }
}
