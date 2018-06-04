<?php

namespace App\Controller;

use App\Entity\Datalist;
use App\Form\DatalistType;
use App\Manager\DatalistManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DatalistController extends Controller
{
    private $datalistManager;

    public function __construct(DatalistManager $dManager)
    {
        $this->datalistManager = $dManager;
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
}
