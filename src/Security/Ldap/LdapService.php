<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 6/3/2018
 * Time: 2:05 PM
 */


namespace App\Security\Ldap;

use App\Entity\LdapUser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Ldap\Ldap;
use Symfony\Component\Ldap\Entry;

class LdapService
{
    private $ldap;

    public function __construct()
    {
        $this->ldap = Ldap::create('ext_ldap', array(
            'host' => 'dns.datacenter.local',
            'port' => 389
        ));

        $this->ldap->bind('CN=Administrator,CN=Users,DC=datacenter,DC=local', 'P@ssword1234');
    }

    public function getLdapUser($username)
    {
        $query = $this->ldap->query('CN=Users,DC=datacenter,DC=local',"(&(sAMAccountName=$username))");

        $results = $query->execute()->toArray();

        if ($results) {
            return $results[0];
        }

        return null;
    }
}