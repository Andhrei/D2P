<?php

namespace App\Controller;

use App\Entity\Datalist;
use App\Form\DatalistType;
use App\Manager\ClientManager;
use App\Manager\DatalistManager;
use App\Manager\DeviceManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DatalistController extends Controller
{
    private $datalistManager;
    private $clientManager;
    private $deviceManager;

    public function __construct(DatalistManager $dManager, ClientManager $clientManager, DeviceManager $deviceManager)
    {
        $this->datalistManager = $dManager;
        $this->clientManager = $clientManager;
        $this->deviceManager  = $deviceManager;
    }

    public function backupDatalist(Request $request, $id) {
        $datalist = $this->datalistManager->find($id);
        $this->datalistManager->backup($datalist);

    }

    public function backup(Request $request)
    {
        $datalist = new Datalist();
        $user = $this->getUser();
        $clients = $user->getClients();
        $devices = $user->getDevices();
        $form = $this->createForm(DatalistType::class, $datalist,array(
            'clients' => $clients,
            'devices' => $devices
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $datalist = $form->getData();

            $datalist = $this->datalistManager->getDatalist($datalist,$user);

            $this->datalistManager->backup($datalist);

            return $this->redirectToRoute('portal_home');
        }

        return $this->render('datalist/newDatalist.html.twig', array(
            'form' => $form->createView(),
            'formMethod' => 'POST',
            'formUrl' => 'api_post_schedule',
            'class' => 'Schedule',
        ));
    }

    public function backupAzure($id)
    {
        $client = $this->clientManager->find($id);
        $user = $this->getUser();
        $azureDevice = $user->getAzureDevice();

        $datalist = $this->datalistManager->getOrCreate($client, $azureDevice, $user);

        $this->datalistManager->backup($datalist);

        return $this->redirectToRoute('portal_home');
    }

    public function backupLocal($id)
    {
        $client = $this->clientManager->find($id);
        $user = $this->getUser();
        $localdevice = $user->getLocalDevice();

        $datalist = $this->datalistManager->getOrCreate($client, $localdevice, $user);

        $this->datalistManager->backup($datalist);

        return $this->redirectToRoute('portal_home');
    }
}
