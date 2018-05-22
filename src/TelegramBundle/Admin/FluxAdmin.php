<?php
/**
 * Created by PhpStorm.
 * User: Vincent
 * Date: 09/09/2016
 * Time: 14:09
 */

namespace TelegramBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class FluxAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('name', 'text')
            ->add('subname', 'text',array('required' => false))
            ->add('partners', 'text',array('required' => false))
            ->add('color', 'sonata_type_color_selector')
            ->add('description', 'textarea',array('required' => false))
            ->add('active', 'checkbox',array('required' => false))
            ->add('archive', 'checkbox',array('required' => false))
            ->add('creationDate', 'date')
            ->add('creationDate', 'date')
            ->add('location','text',array('required' => false))
            ->add('updating', 'checkbox',array('required' => false))
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('name');
    }
}