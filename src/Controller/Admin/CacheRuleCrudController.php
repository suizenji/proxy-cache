<?php

namespace App\Controller\Admin;

use App\Entity\CacheRule;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CacheRuleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return CacheRule::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
