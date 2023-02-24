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
        yield ChoiceField::new('judgeType')->setChoices([
            CacheRule::JUDGE_TYPE_SCHEME_HOST => CacheRule::JUDGE_TYPE_SCHEME_HOST,
        ]);
        yield TextField::new('judgeCond');
        yield ChoiceField::new('resType')->setChoices([
            CacheRule::RES_TYPE_URL_MATCH => CacheRule::RES_TYPE_URL_MATCH,
            CacheRule::RES_TYPE_SCHEME_HOST_MATCH => CacheRule::RES_TYPE_SCHEME_HOST_MATCH,
        ]);
        yield TextField::new('resCond');
    }
}
