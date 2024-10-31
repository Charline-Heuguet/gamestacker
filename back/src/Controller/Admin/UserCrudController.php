<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Field\VichFileField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
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
            IdField::new('id')->onlyOnIndex(),
            TextField::new('pseudo'),
            TextField::new('discord'),
            TextField::new('email')->onlyOnIndex(),
            TextEditorField::new('description'),

            // Champ pour uploader l'image de profil avec VichUploader
            VichFileField::new('imageFile', 'Photo de profil')
                ->onlyOnForms(),

            // Affiche l'image actuelle dans la liste et les détails
            ImageField::new('image')
                ->setBasePath('/images/users') // Assure-toi que le chemin correspond à ta config VichUploader
                ->onlyOnIndex(),

            AssociationField::new('platform', 'Platforms')
                ->formatValue(function ($value, $entity) {
                    return implode(', ', array_map(function ($platform) {
                        return $platform->getName();
                    }, $entity->getPlatform()->toArray()));
                }),

            ArrayField::new('comment', 'Comments')
                ->formatValue(function ($value, $entity) {
                    $comments = $entity->getComment()->toArray();
                    usort($comments, function ($a, $b) {
                        return $b->getDate() <=> $a->getDate();
                    });
                    $comments = array_slice($comments, 0, 3);
                    return implode('<br>', array_map(function ($comment) {
                        return sprintf(
                            '<strong>%s</strong>: %s',
                            $comment->getDate()->format('d-m-Y H:i:s'),
                            $comment->getContent()
                        );
                    }, $comments));
                })->onlyOnDetail(),

            AssociationField::new('forums', 'Forums')
                ->formatValue(function ($value, $entity) {
                    return implode(', ', array_map(function ($forum) {
                        return $forum->getTitle();
                    }, $entity->getForums()->toArray()));
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
