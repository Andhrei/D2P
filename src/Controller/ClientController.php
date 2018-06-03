<?php

namespace App\Controller;

use App\Entity\Client;
use App\Manager\ClientManager;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ClientController extends Controller
{
    private $manager;

    public function __construct(ClientManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @Route("/client", name="client")
     */
    public function index()
    {
        return $this->render('client/index.html.twig', [
            'controller_name' => 'ClientController',
        ]);
    }

    public function new(Request $request)
    {
        $client = new Client();
        $form = $this->createFormBuilder($client)
            ->add('name', TextType::class)
            ->add('hostname', TextType::class)
            ->add('save', SubmitType::class, array('label' => 'Create Client'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $client = $form->getData();

            $this->manager->addClientFor($client,$this->getUser());

            return $this->redirectToRoute('portal_home');
        }

        return $this->render('client/newClient.html.twig', array(
            'form' => $form->createView(),
            'formMethod' => 'POST',
            'formUrl' => 'api_post_client',
            'class' => 'Client',
        ));
    }
}
