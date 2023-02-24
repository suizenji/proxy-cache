<?php

namespace App\Controller\Admin;

use App\Entity\HttpContext;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use Symfony\Component\HttpFoundation\Request;

class HttpContextCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return HttpContext::class;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(ChoiceFilter::new('type')->setChoices([
                HttpContext::TYPE_RECV => HttpContext::TYPE_RECV,
                HttpContext::TYPE_SEND => HttpContext::TYPE_SEND,
            ]))
            ->add(ChoiceFilter::new('f1')->setChoices([
                Request::METHOD_GET => Request::METHOD_GET,
                Request::METHOD_POST => Request::METHOD_POST,
            ]))
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('tranId');
        yield TextField::new('type');
        yield TextField::new('f1');
        yield TextField::new('f2');
        yield TextField::new('f3');
    }
}
