<?php

namespace App\Controller\Admin;

use App\Entity\Commentaires;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CommentairesCrudController extends AbstractCrudController
{

    public static function getEntityFqcn(): string
    {
        return Commentaires::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
        IdField::new('id'),
        AssociationField::new('post'),
        AssociationField::new('com_parent'),
        TextField::new('contenu'),
        ImageField::new('img'),
        ImageField::new('video'),
        DateTimeField::new('creation_date'),
        AssociationField::new('userID'),
        ];
    }
}
