<?php 

namespace App\Controller\Admin;

use App\Entity\ReportCategory;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ReportCategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ReportCategory::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name'),  
    
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL); // Ajoute l'action de détail à la page index
    }
}