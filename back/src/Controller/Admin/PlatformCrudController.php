<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Platform;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PlatformCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Platform::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            
            TextField::new('name'),  
        ];
    }
}
