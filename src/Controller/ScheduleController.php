<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Device;
use App\Entity\LdapUser;
use App\Entity\Schedule;
use App\Manager\ClientManager;
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

    public function __construct(ScheduleManager $schedManager, ClientManager $clientManager)
    {
        $this->schedManager = $schedManager;
        $this->clientManager = $clientManager;
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
        $specification = new Schedule();
        $user = $this->getUser();
        $clients = $user->getClients();
        $devices = $user->getDevices();
        $form = $this->createFormBuilder($specification)
            ->add('client', EntityType::class, array(
                'class' => Client::class,
                'choices' => $clients,
                'choice_label' => 'hostname'
            ))
            ->add('device', EntityType::class, array(
                'class' => Device::class,
                'choices' => $devices,
                'choice_label' => 'type'
            ))
            ->add('recurrence', ChoiceType::class, array(
                'choices' => array(
                    'Daily' => 'DAILY',
                    'Weekly' => 'WEEKLY'
                )
            ))
            ->add('save', SubmitType::class, array('label' => 'Create Schedule'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $schedule = $form->getData();

            $this->schedManager->addScheduleForUser($schedule,$user);

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
