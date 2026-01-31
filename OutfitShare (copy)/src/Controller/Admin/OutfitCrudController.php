<?php

namespace App\Controller\Admin;

use App\Entity\Outfit;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class OutfitCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Outfit::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('titulo');
        yield TextEditorField::new('descripcion');
        yield AssociationField::new('user', 'Autor');
        // CollectionField para prendas es complejo de editar inline, pero útil para ver
        yield CollectionField::new('prendas')->hideOnForm();
        yield NumberField::new('likes.count', 'Likes')->hideOnForm();
    }
}
