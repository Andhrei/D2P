<?php

namespace App\Manager;

use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\Common\Persistence\Proxy;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use App\Model\ManagerInterface;

/**
 * Class BaseManager
 * @package Publicis\Bundle\CoreBundle\Manager
 */
abstract class BaseManager implements ManagerInterface
{
    /** @var  EntityManager $em */
    protected $em;

    /** @var */
    protected $class;
    /** @var */
    protected $serviceName;

    /**
     * @param EntityManager $em
     */
    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->em;
    }

    /**
     * @param $name
     */
    public function setServiceName($name)
    {
        $this->serviceName = $name;
    }

    /**
     * @param $class
     */
    public function setEntityClass($class)
    {
        $this->class = $class;
    }

    public function getEntityClass()
    {
        return $this->class;
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getRepository()
    {
        return $this->em->getRepository($this->class);
    }

    public function isEntity($class)
    {
        if (is_object($class)) {
            $class = ($class instanceof Proxy) ? get_parent_class($class) : get_class($class);
        }

        return ! $this->em->getMetadataFactory()->isTransient($class);
    }

    /**
     * Finds an entity by its primary key / identifier.
     *
     * @param mixed $id The identifier.
     * @param int $lockMode The lock mode.
     * @param int|null $lockVersion The lock version.
     *
     * @return object|null The entity instance or NULL if the entity can not be found.
     */
    public function find($id, $lockMode = LockMode::NONE, $lockVersion = null)
    {
        return $this->getRepository()->find($id, $lockMode, $lockVersion);
    }

    /**
     * Finds all entities in the repository.
     *
     * @return array The entities.
     */
    public function findAll()
    {
        return $this->getRepository()->findAll();
    }

    /**
     * Finds entities by a set of criteria.
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return array The objects.
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Finds entities by a set of criteria.
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return array The objects.
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * @param $filters
     * @param bool $isSearch
     * @return mixed
     */
    public function createQueryByFilters($filters, $isSearch = false)
    {
        return $this->getRepository()->createQueryByFilters($filters, $isSearch);
    }

    /**
     * Get list of [id-toString]
     */
    public function getListForSelector($field)
    {
        $datas = $this->getRepository()->getListForSelector();
        $result = [];
        foreach ($datas as $data) {
            $result[$data['id']] = $data[$field];
        }

        return $result;
    }


    /**
     * @param $entity
     * @param bool $flush
     */
    public function save($entity, $flush = true)
    {
        $this->em->persist($entity);
        if ($flush) {
            $this->em->flush();
        }
    }

    public function flush($clear = false)
    {
        $this->em->flush();
        if ($clear) {
            $this->em->clear();
        }
    }

    /**
     * @param $entity
     * @param bool $flush
     */
    public function delete($entity, $flush = true)
    {
        $this->em->remove($entity);
        if ($flush) {
            $this->em->flush();
        }
    }

    public function getFormProcessRedirectRouteName()
    {
        return '';
    }

}