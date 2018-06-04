<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 6/3/2018
 * Time: 4:22 PM
 */

namespace App\Manager;


use App\Entity\Client;
use App\Entity\LdapUser;
use App\Repository\ClientRepository;
use App\Repository\LdapUserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Ldap\Entry;
use Symfony\Component\Process\Process;

class ClientManager extends BaseManager
{

    public function __construct(ClientRepository $repository, EntityManagerInterface $entityManager)
    {
        $this->class = Client::class;
        $this->repo = $repository;
        $this->em = $entityManager;
    }

    public function filterBy($filters)
    {
        // TODO: Implement filterBy() method.
    }

    public function addClientFor(Client $client, LdapUser $user)
    {
        $result= $this->findOneBy(['hostname' => $client->getHostname() ]);
        if ($result) {
            $client = $result;
            $client->addUser($user);
        } else {
            $file = '../clientInfiles/'.$client->getHostname();
            $filesystem = new Filesystem();
            $filesystem->touch($file);
            $filesystem->dumpFile($file,'-host '.$client->getHostname().' -da A.10.03 -push_inst "C:\Program Files\OmniBack\" "C:\ProgramData\OmniBack\" "Administrator" "P@ssword1234" "dataprotector.datacenter.local" 2 1');

            $process = new Process("ob2install -server dataprotector.datacenter.local -input $file");
//            $process = new Process("omnicellinfo -cell");
            $process->start();
//            $process->run(function ($type, $buffer) {
//                if (Process::ERR === $type) {
//                    dump('ERR > '.$buffer);
//                } else {
//                    dump( 'OUT > '.$buffer);
//                }
//            });

            $client->addUser($user);
}

        $this->save($client);
}

}