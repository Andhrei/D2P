<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Datalist;
use App\Entity\Device;
use App\Entity\LdapUser;
use App\Entity\Schedule;
use App\Form\ScheduleType;
use App\Manager\ClientManager;
use App\Manager\DatalistManager;
use App\Manager\ScheduleManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ScheduleController extends Controller
{
    private $schedManager;
    private $clientManager;
    private $datalistManager;

    public function __construct(ScheduleManager $schedManager, ClientManager $clientManager, DatalistManager $datalistManager)
    {
        $this->schedManager = $schedManager;
        $this->clientManager = $clientManager;
        $this->datalistManager = $datalistManager;
    }
    /**
     * @Route("/schedule", name="schedule")
     */
    public function index()
    {
        return $this->render('schedule/index.html.twig', [
            'controller_name' => 'ScheduleController',
        ]);
    }

    public function new(Request $request)
    {
        $schedule = new Schedule();
        $datalist = new Datalist();
        $schedule->setDatalist($datalist);
        $user = $this->getUser();
        $clients = $user->getClients();
        $devices = $user->getDevices();
        $form = $this->createForm(ScheduleType::class, $schedule,array(
            'allow_extra_fields' => true,
            'clients' => $clients,
            'devices' => $devices
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $schedule = $form->getData();
            $datalist = $this->datalistManager->getOrCreate($datalist->getClient(),$datalist->getDevice(),$user);
            $schedule->setDatalist($datalist);
            $schedule->setName($datalist->getName()."_".$schedule->getRecurrence());


            $this->schedManager->save($schedule);
            $this->schedManager->schedule($schedule);

            return $this->redirectToRoute('portal_home');
        }

        return $this->render('schedule/newSchedule.html.twig', array(
            'form' => $form->createView(),
            'formMethod' => 'POST',
            'formUrl' => 'api_post_schedule',
            'class' => 'Schedule',
        ));
    }
}
