<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;


class CommentArticleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Comment::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextareaField::new('content'),
            AssociationField::new('article')->setLabel('Article'),
            AssociationField::new('user')->setLabel('User'),
        ];
    }

    // Méthode pour filtrer les commentaires d'articles uniquement
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        // Appel du QueryBuilder parent
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        // Ajouter une condition pour récupérer uniquement les commentaires associés à un article
        $qb->andWhere('entity.article IS NOT NULL')
            ->andWhere('entity.forum IS NULL'); // Exclure les commentaires liés aux forums

        return $qb;
    }
}
