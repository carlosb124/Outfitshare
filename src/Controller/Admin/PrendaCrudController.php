<?php

namespace App\Controller\Admin;

use App\Entity\Prenda;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class PrendaCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Prenda::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Prenda')
            ->setEntityLabelInPlural('Prendas')
            ->setSearchFields(['nombre', 'marca', 'categoria'])
            ->setDefaultSort(['id' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new ('id')->hideOnForm();
        yield TextField::new ('nombre', 'Nombre');
        yield TextField::new ('marca', 'Marca');
        yield TextField::new ('categoria', 'Categoría');
        yield AssociationField::new ('user', 'Dueño');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }
}