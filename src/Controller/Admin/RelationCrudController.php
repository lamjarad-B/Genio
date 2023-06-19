<?php

namespace App\Controller\Admin;

use App\Entity\Relation;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class RelationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Relation::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('personne1'),
            AssociationField::new('relation_type'),
            AssociationField::new('personne2'),
        ];
    }
    
}
