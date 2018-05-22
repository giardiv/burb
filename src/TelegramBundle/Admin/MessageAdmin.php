<?php

namespace TelegramBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class MessageAdmin extends AbstractAdmin
{
protected function configureFormFields(FormMapper $formMapper)
{
    $formMapper->add('text','textarea')
        ->add('type', 'text')
        ->add('flux','text')
        ->add("user", 'text')
        ->add("date", 'date')
    ;
}

protected function configureDatagridFilters(DatagridMapper $datagridMapper)
{
$datagridMapper->add('text')->add('flux');
}

protected function configureListFields(ListMapper $listMapper)
{
$listMapper->addIdentifier('tid')->add('text')->add('type')->add('user')->add('flux')->add('date');
}
}