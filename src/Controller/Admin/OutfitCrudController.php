<?php

namespace App\Controller\Admin;

use App\Entity\Outfit;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;

class OutfitCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Outfit::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Outfit')
            ->setEntityLabelInPlural('Outfits')
            ->setSearchFields(['titulo', 'descripcion'])
            ->setDefaultSort(['fechaPublicacion' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new ('id')->hideOnForm();
        yield TextField::new ('titulo', 'Título');
        yield TextareaField::new ('descripcion', 'Descripción')->hideOnIndex();
        yield AssociationField::new ('user', 'Autor');
        yield DateTimeField::new ('fechaPublicacion', 'Publicado');
        yield IntegerField::new ('likes.count', 'Likes')->hideOnForm()->formatValue(fn($value, $entity) => count($entity->getLikes()));
        yield IntegerField::new ('comments.count', 'Comentarios')->hideOnForm()->formatValue(fn($value, $entity) => count($entity->getComments()));
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }
}