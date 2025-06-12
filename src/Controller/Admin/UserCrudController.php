<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
        TextField::new('username'),
        TextField::new('email'),
        ArrayField::new('roles')->setHelp('["ROLE_USER"]'),
        ImageField::new('pp_img')
        ->setBasePath('uploads/pp')
        ->setUploadDir('public/uploads/pp')
        ->setRequired(false)
        ->setHelp('Upload a profile picture'),
        ];
    }
}
