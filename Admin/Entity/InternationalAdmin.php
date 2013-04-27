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
        $entity = $this->getSubject();
        $id = $entity->getId();

        if (is_null($id)) {
            $language = $this->modelManager
                    ->getEntityManager('Raindrop\LocaleBundle\Entity\Language')
                    ->getRepository('Raindrop\LocaleBundle\Entity\Language')
                    ->createQueryBuilder('l')
                    ->where('l.id NOT IN (SELECT il.id FROM RaindropLocaleBundle:International i LEFT JOIN i.language il)');

            $country = $this->modelManager
                    ->getEntityManager('Raindrop\LocaleBundle\Entity\Country')
                    ->getRepository('Raindrop\LocaleBundle\Entity\Country')
                    ->createQueryBuilder('c')
                    ->where('c.id NOT IN (SELECT ic.id FROM RaindropLocaleBundle:International i LEFT JOIN i.countries ic)');
        } else {
            $language = $this->modelManager
                    ->getEntityManager('Raindrop\LocaleBundle\Entity\Language')
                    ->getRepository('Raindrop\LocaleBundle\Entity\Language')
                    ->createQueryBuilder('l')
                    ->where('l.id NOT IN (SELECT il.id FROM RaindropLocaleBundle:International i LEFT JOIN i.language il WHERE i.id <> :id)')
                    ->setParameter('id', $id);

            $country = $this->modelManager
                    ->getEntityManager('Raindrop\LocaleBundle\Entity\Country')
                    ->getRepository('Raindrop\LocaleBundle\Entity\Country')
                    ->createQueryBuilder('c')
                    ->where('c.id NOT IN (SELECT ic.id FROM RaindropLocaleBundle:International i LEFT JOIN i.countries ic WHERE i.id <> :id)')
                    ->setParameter('id', $id);
        }

        $formMapper
            ->add('language', null, array('required' => true, 'query_builder' => $language))
            ->add('countries', null, array('required' => true, 'query_builder' => $country));
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
    }
}
