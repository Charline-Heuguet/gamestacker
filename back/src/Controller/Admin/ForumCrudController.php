<?php

namespace App\Controller\Admin;

use DateTime;
use App\Entity\Forum;
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

class ForumCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Forum::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title'),
            TextField::new('content'),
            AssociationField::new('user', 'Creator'),
            DateTimeField::new('date')
            ->setFormat('dd/MM/yyyy'),
            ArrayField::new('comment', 'Commentaires')
            ->formatValue(function ($value, $entity) {
                // Récupérer les commentaires et les trier du plus récent au plus ancien
                $comments = $entity->getComment()->toArray();
                usort($comments, function ($a, $b) {
                    return $b->getDate() <=> $a->getDate(); // Tri par date décroissante
                });

                // Afficher tous les commentaires
                return implode('<br>', array_map(function ($comment) {
                    // Vérifier si la date existe, sinon afficher 'Pas de date'
                    $date = $comment->getDate() ? $comment->getDate()->format('d/m/Y') : 'Pas de date';
            
                    return sprintf(
                        '<strong>%s</strong>: %s',
                        $date,  // Afficher la date du commentaire ou 'Pas de date'
                        $comment->getContent()  // Afficher le contenu du commentaire
                    );
                }, $comments));
            })
            ->onlyOnDetail(),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
            
    }
}