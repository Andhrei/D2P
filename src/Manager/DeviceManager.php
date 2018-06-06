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
        $cloudOption  = 'Gv7lS9VKzANkV/PDcY5QhyJjFCzXV3QJ5FfqkCLetJAdJixJDbKc7OwsGKf+Qt57PQzXgXWPhn5hGqxkHPhqpqWd/98Rpd+bToaQKDa0ODQeKuAshn01RUva9N7L30T4C8t60R8d1tvCWrKVYF2PEYCdfcaj2hya2LliKJ4z/cYgSBYwt3ssdr8q3Mkj4I1bXgg6CiuYICn8ZIdPG1NE7m/R3dg+8Uv8UVyQThWEaJEX56uWKHeClTZkIWdeEFTx7JQszGdGzeVu5MAvBImUhhUEHWjoKa21aEKDXCSS5xGuYWheW2TN4dhjuI8iESUCx5k2S9f0zfJoSw==';

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

    public function createLocalDeviceForUser(LdapUser $user) {
        $soStore = $user->getUsername().'_sos';
        $libraryName = $user->getUsername()."_so";
        $directory = '\\\\dataprotector.datacenter.local\D2P';
        $deviceName = $user->getUsername()."_gw_so";
        $mediaPool = $user->getUsername()."_somp";

        /*  user's StoreOnce store */
        $cmd= 'storeoncesoftware --create_store --name=Store_'.$soStore.' --store_description="Data Protector Store"';
        dump($cmd);
        $proc = new Process('storeoncesoftware --create_store --name=Store_'.$soStore.' --store_description="Data Protector Store"');
        $proc->start();



        /*  user's StoreOnce library */
        $file = '../deviceInfiles/'.$user->getUsername().'_so_library';
        $filesystem = new Filesystem();
        $filesystem->touch($file);
        $filesystem->dumpFile($file,'');
        $filesystem->appendToFile($file,"NAME \"$libraryName\"\xA");
        $filesystem->appendToFile($file,"DESCRIPTION \"\"\xA");
        $filesystem->appendToFile($file,"POLICY BackupToDisk\xA");
        $filesystem->appendToFile($file,"TYPE StoreOnceSoftware\xA");
        $filesystem->appendToFile($file,"DIRECTORY\xA");
        $filesystem->appendToFile($file,"\t\"$directory\"\xA");
        $filesystem->appendToFile($file,"MGMTCONSOLEURL \"\"\xA");

        $cmd="omniupload -create_library ".$file;
        dump($cmd);
        $process = new Process($cmd);
        $process->run();

        /*  user's StoreOnce media pool */
        $cmd = "omnimm -create_pool ".$mediaPool." \"StoreOnce software deduplication\" App+Loose 0 0";
        dump($cmd);
        $process = new Process($cmd);
        $process->run();

        /*  user's StoreOnce device */
        $file = '../deviceInfiles/'.$user->getUsername().'_so_device';
        $filesystem->touch($file);
        $filesystem->dumpFile($file,'');
        $filesystem->appendToFile($file,"NAME \"$deviceName\"\xA");
        $filesystem->appendToFile($file,"DESCRIPTION \"\"\xA");
        $filesystem->appendToFile($file,"HOST dataprotector.datacenter.local\xA");
        $filesystem->appendToFile($file,"POLICY BackupToDisk\xA");
        $filesystem->appendToFile($file,"TYPE StoreOnceSoftware\xA");
        $filesystem->appendToFile($file,"POOL \"$mediaPool\"\xA");
        $filesystem->appendToFile($file,"LIBRARY \"$libraryName\"\xA");
        $filesystem->appendToFile($file,"VERIFY\xA");
        $filesystem->appendToFile($file,"RESTOREDEVICEPOOL YES\xA");
        $filesystem->appendToFile($file,"COPYDEVICEPOOL YES\xA");

        $cmd = "omniupload -create_device ".$file;
        dump($cmd);
        $process = new Process($cmd);
        $process->run();

        $device = new Device();
        $device->setName($deviceName);
        $device->setLibrary($libraryName);
        $device->setType("Local");
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