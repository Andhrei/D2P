<?php

namespace App\Controller;

use App\Entity\Client;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ClientController extends Controller
{
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

        return $this->render('client/newClient.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
