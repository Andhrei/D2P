<?php

namespace App\Manager;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use App\Manager\BaseManager;
use App\Model\ManagerInterface;


/**
 * Class UserManager
 * @package Publicis\Bundle\TraficBundle\Manager
 */
class UserManager extends BaseManager
{

    public function __construct(UserRepository $repository)
    {

    }

    public function filterBy($filters)
    {
        return $this->getRepository()->filterBy($filters);

    }

    public function getName()
    {
        return 'user';
    }
}