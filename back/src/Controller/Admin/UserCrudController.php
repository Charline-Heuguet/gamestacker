<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('pseudo'),
            TextField::new('discord'),
            TextField::new('email'),
            TextEditorField::new('description'),
            AssociationField::new('platform', 'Platforms')
                ->formatValue(function ($value, $entity) {
                    // Boucler sur les catégories et afficher leur nom
                    return implode(', ', array_map(function ($plateform) {
                        return $plateform->getName(); 
                    }, $entity->getPlatform()->toArray()));
                }),
                ArrayField::new('comment', 'Comments')
                    ->formatValue(function ($value, $entity) {
                    // Récupérer les commentaires et les trier du plus récent au plus ancien
                    $comments = $entity->getComment()->toArray();
                    usort($comments, function ($a, $b) {
                    return $b->getDate() <=> $a->getDate(); // Tri par date décroissante
                });

                // Limiter aux 3 derniers commentaires
                $comments = array_slice($comments, 0, 3);

                 // Formater les commentaires pour l'affichage
                return implode('<br>', array_map(function ($comment) {
                    return sprintf(
                    '<strong>%s</strong>: %s', 
                    $comment->getDate()->format('d-m-Y H:i:s'), // Afficher la date
                        $comment->getContent()  // Afficher le contenu du commentaire
                    );
                },    $comments));
            }),

            AssociationField::new('forums', 'Forums')
                ->formatValue(function ($value, $entity) {
                    // Boucler sur les forums et afficher leur titre
                    return implode(', ', array_map(function ($forum) {
                        return $forum->getTitle(); 
                    }, $entity->getForums()->toArray()));
                })
            ->onlyOnDetail()
            
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL); // Ajoute l'action de détail à la page index
    }
}
