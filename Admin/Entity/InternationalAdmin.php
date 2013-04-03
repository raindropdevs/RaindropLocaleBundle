<?php

namespace Raindrop\LocaleBundle\Admin\Entity;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * International Admin
 */
class InternationalAdmin extends Admin
{
    /**
     * {@inheritdoc}
     */    
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('__toString', null, array('label' => 'Name'))
            ->add('language')
            ->add('countries');
    }
    
    /**
     * {@inheritdoc}
     */    
    protected function configureFormFields(FormMapper $formMapper)
    {
        $query = $this->modelManager
                ->getEntityManager('Raindrop\LocaleBundle\Entity\Language')
                ->createQuery('SELECT l FROM Raindrop\LocaleBundle\Entity\Language l WHERE l.id NOT IN (SELECT la.id FROM RaindropLocaleBundle:International i LEFT JOIN i.language la)');

        $formMapper
            ->add('language', 'sonata_type_model', array('required' => true, 'query' => $query))
            ->add('countries');
    }   
    
    /**
     * {@inheritdoc}
     */    
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
    }    
}
