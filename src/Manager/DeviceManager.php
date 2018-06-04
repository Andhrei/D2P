<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 6/3/2018
 * Time: 4:22 PM
 */

namespace App\Manager;


use App\Entity\Client;
use App\Entity\Device;
use App\Entity\LdapUser;
use App\Repository\ClientRepository;
use App\Repository\DeviceRepository;
use App\Repository\LdapUserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Ldap\Entry;
use Symfony\Component\Process\Process;

class DeviceManager extends BaseManager
{

    public function __construct(DeviceRepository $repository, EntityManagerInterface $entityManager)
    {
        $this->class = Device::class;
        $this->repo = $repository;
        $this->em = $entityManager;
    }

    public function filterBy($filters)
    {
        // TODO: Implement filterBy() method.
    }

    public function createAzureDeviceForUser(LdapUser $user)
    {
        $deviceName = $user->getUsername()."_gw_az";
        $libraryName = $user->getUsername()."_az";
        $mediaPool = $user->getUsername()."_mp";

        $azureContainer = '\\\\azstrg1.blob.core.windows.net\d2p';
        $cloudOption  = 'T9zxEb2ybDv86kifY0i48Gdes1JFqg4pSa2y3UZKlW3tViKrytb6XcpomiWEJaHhsX1gZ78xCxNTw8UfJCVD0uwZ5Xciy9BJtabFQmkFt6OtEluW4nXSG8ehm/E0jNz9c145bYX2O+VrGryIpfHrlMPxb5j4TSZ5J89yzqBOCVDEm2ynFif9P81jluvbSfAlMHpbzgm6r9BwMNwGeuj/xyRW1fB5azrJshW+eXOrhI3SmsUDho6NTZ3e4g+9NfB0pn8VSVXJzz7QTfWUIFEzpxEcFL5NoCao6Vqd589MLzod3y4LWT7JoWih4m10l6HedNGDqUyhZFVuFw==';

        $file = '../deviceInfiles/'.$user->getUsername().'_az_library';
        $filesystem = new Filesystem();
        $filesystem->touch($file);
        $filesystem->dumpFile($file,'');
        $filesystem->appendToFile($file,"NAME \"$libraryName\"\xA");
        $filesystem->appendToFile($file,"DESCRIPTION \"\"\xA");
        $filesystem->appendToFile($file,"POLICY BackupToDisk\xA");
        $filesystem->appendToFile($file,"TYPE CloudAzure\xA");
        $filesystem->appendToFile($file,"DIRECTORY\xA");
        $filesystem->appendToFile($file,"\t\"$azureContainer\"\xA");
        $filesystem->appendToFile($file,"CLOUDOPTIONS \"$cloudOption\"\xA");
        $filesystem->appendToFile($file,"MGMTCONSOLEURL \"https://portal.azure.com\"\xA");


        $process = new Process("omniupload -create_library ".$file);
        $process->run();

        $process = new Process("omnimm -create_pool ".$mediaPool." \"Azure-Cloud\" App+Loose 0 0");
        $process->run();

        $file = '../deviceInfiles/'.$user->getUsername().'_az_device';
        $filesystem->touch($file);
        $filesystem->dumpFile($file,'');
        $filesystem->appendToFile($file,"NAME \"$deviceName\"\xA");
        $filesystem->appendToFile($file,"DESCRIPTION \"\"\xA");
        $filesystem->appendToFile($file,"HOST dataprotector.datacenter.local\xA");
        $filesystem->appendToFile($file,"POLICY BackupToDisk\xA");
        $filesystem->appendToFile($file,"TYPE CloudAzure\xA");
        $filesystem->appendToFile($file,"POOL \"$mediaPool\"\xA");
        $filesystem->appendToFile($file,"LIBRARY \"$libraryName\"\xA");
        $filesystem->appendToFile($file,"VERIFY\xA");
        $filesystem->appendToFile($file,"RESTOREDEVICEPOOL YES\xA");
        $filesystem->appendToFile($file,"COPYDEVICEPOOL YES\xA");


        $process = new Process("omniupload -create_device ".$file);
        $process->run();

        $device = new Device();
        $device->setName($deviceName);
        $device->setLibrary($libraryName);
        $device->setType("Azure");
        $device->setUser($user);

        $this->save($device);

        return $device;
    }

    public function addDeviceForUser(Device $device,string $type, LdapUser $user)
    {
        $file = '../deviceInfiles/'.$user->getUsername().'_'.$type.' library';
        $filesystem = new Filesystem();
        $filesystem->touch($file);
        $filesystem->dumpFile($file,'');
        $filesystem->appendToFile($file,'NAME '.$user->getUsername().'_'.$type.' \n');
        $filesystem->appendToFile($file,'DESCRIPTION ""\n');
        $filesystem->appendToFile($file,'POLICY BackupToDisk\n');
        $filesystem->appendToFile($file,'TYPE CloudAzure\n');
        $filesystem->appendToFile($file,'DIRECTORY\n');
        $filesystem->appendToFile($file,'\t"\\azstrg1.blob.core.windows.net\dataprotector"\n');
        $filesystem->appendToFile($file,'CLOUDOPTIONS "BBHtdQrvI3YD5aEqnZNKY1doahdVUMKkBI1NonR7kNVsmfsUK76sxIAtlygKyCNvgzNzwCV6HuLLVTTt+e8jsR4MuQGkWUNdBM7UFN1vJwO5sh8j7qwFb9g1gSmHiql4FD8JzGogvMf7OfgFM6gvL5azrS5BFrkve/0C5ypJNW1s19dH/RvWmD9DeLIhWzI1hqWczXpkdE28ZdSjs7wAh7HgBmMCQoC+gRC6qGhYfgAZXXrFseb/fhzTnrKDCzSu9LSVbfujQHt24tEVwN5ZkZn8AC5UsA+7CXDpCEtJ9x+E/YvxsE0zwMQekN15sI67n7shNPhKhDBTkw=="\n');
        $filesystem->appendToFile($file,'MGMTCONSOLEURL "https://portal.azure.com"\n');

//        $process = new Process("ob2install -server dataprotector.datacenter.local -input $file");
//            $process = new Process("omnicellinfo -cell");
//        $process->start();
//            $process->run(function ($type, $buffer) {
//                if (Process::ERR === $type) {
//                    dump('ERR > '.$buffer);
//                } else {
//                    dump( 'OUT > '.$buffer);
//                }
//            });

    }

}