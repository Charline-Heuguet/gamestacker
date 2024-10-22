<?php 

namespace App\Controller\Admin;

use App\Entity\Announcement;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class AnnouncementCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Announcement::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title'),
            TextField::new('content'),
            TextField::new('game'),
            DateTimeField::new('date'),
            IntegerField::new('max_nb_players')
            ->onlyOnDetail(),
            AssociationField::new('category', 'Categories')
                ->formatValue(function ($value, $entity) {
                    return implode(', ', array_map(function ($category) {
                        return $category->getName();
                    }, $entity->getCategory()->toArray()));
                }),
            AssociationField::new('user', 'User'),
            AssociationField::new('participants', 'Participants')
                ->formatValue(function ($value, $entity) {
                    return implode('<br>', array_map(function ($participant) {
                        return $participant->getPseudo();
                    }, $entity->getParticipants()->toArray()));
                }),     
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL); // Ajoute l'action de détail à la page index
    }
}