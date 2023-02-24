<?php

namespace App\Controller\Admin;

use App\Entity\CacheRule;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CacheRuleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return CacheRule::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Cache Rule')
            ->setEntityLabelInPlural('Cache Rules')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield ChoiceField::new('judgeType');
        yield TextField::new('judgeCond');
        yield ChoiceField::new('resType');
        yield TextField::new('resCond');
    }
}
