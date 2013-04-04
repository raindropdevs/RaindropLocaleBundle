<?php

namespace Raindrop\LocaleBundle\Admin\Entity;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * Country Admin
 */
class CountryAdmin extends Admin
{
    /**
     * {@inheritdoc}
     */    
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('code')
            ->add('name')
            ->add('defaultLanguage')
            ->add('languages');
    }
    
    /**
     * {@inheritdoc}
     */    
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('code')
            ->add('name')
            ->add('enabled', null, array('required' => false))
            ->add('defaultLanguage', null, array('required' => false))
            ->add('languages', null, array('required' => false));
    }   
    
    /**
     * {@inheritdoc}
     */    
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name');
    }    
}