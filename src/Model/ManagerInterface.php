<?php


namespace App\Model;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManager;

interface ManagerInterface
{
    public function setEntityManager(EntityManager $em);
    public function setEntityClass($class);
    public function getEntityClass();
    public function setServiceName($name);
    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getRepository();
    public function find($id, $lockMode = LockMode::NONE, $lockVersion = null);
    public function findAll();
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);
    public function findOneBy(array $criteria, array $orderBy = null);

    public function createQueryByFilters($filters, $isSearch = false);
    public function filterBy($filters);
    public function getListForSelector($field);


    public function getFormProcessRedirectRouteName();

}