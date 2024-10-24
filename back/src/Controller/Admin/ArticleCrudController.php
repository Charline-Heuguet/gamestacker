<?php

namespace App\Controller\Admin;

use DateTime;
use App\Entity\Article;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
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
            TextField::new('image'),

            // Utiliser AssociationField pour les catégories
            AssociationField::new('category', 'Categories')
                ->formatValue(function ($value, $entity) {
                    // Boucler sur les catégories et afficher leur nom
                    return implode(', ', array_map(function ($category) {
                        return $category->getName(); // Remplace getName par la méthode pour afficher le nom de la catégorie
                    }, $entity->getCategory()->toArray()));
                }),

            // Utiliser ArrayField pour afficher les commentaires
            ArrayField::new('comment', 'Comments')
                ->formatValue(function ($value, $entity) {
                // Récupérer les 3 derniers commentaires
                $comments = array_slice($entity->getComment()->toArray(), -3);

        // Afficher les 3 derniers commentaires avec le pseudo de l'utilisateur
                return implode('<br>', array_map(function ($comment) {
                    $user = $comment->getUser();
                    $pseudo = $user ? $user->getPseudo() : 'Anonymous';
                    return sprintf(
                '<strong>%s</strong>: %s', 
                $pseudo, 
                        $comment->getContent()
            );
        }, $comments));
    }),

        
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL); // Ajoute l'action de détail à la page index
    }
}
