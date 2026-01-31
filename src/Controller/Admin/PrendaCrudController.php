<?php

namespace App\Controller\Admin;

use App\Entity\Prenda;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PrendaCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Prenda::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('nombre');
        yield TextField::new('marca');
        yield TextField::new('categoria');
        yield ImageField::new('imagen')
            ->setBasePath('uploads/images')
            ->setUploadDir('public/uploads/images')
            ->setUploadedFileNamePattern('[randomhash].[extension]')
            ->setRequired(false);
        yield AssociationField::new('user', 'Propietario');
    }
}
