<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield \EasyCorp\Bundle\EasyAdminBundle\Field\IdField::new('id')->hideOnForm();
        yield \EasyCorp\Bundle\EasyAdminBundle\Field\TextField::new('email');
        yield \EasyCorp\Bundle\EasyAdminBundle\Field\TextField::new('name');
        yield \EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField::new('roles');
        yield \EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField::new('isBanned')->renderAsSwitch(false);
        yield \EasyCorp\Bundle\EasyAdminBundle\Field\NumberField::new('puntos');
    }
}
