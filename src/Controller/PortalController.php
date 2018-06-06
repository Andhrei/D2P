<?php
/**
 * Created by PhpStorm.
 * LdapUser: Administrator
 * Date: 5/31/2018
 * Time: 3:26 PM
 */

namespace App\Controller;

use App\Entity\LdapUser;
use App\Manager\DatalistManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;


class PortalController extends BaseController
{
    private $datalistManager;

    public function __construct(DatalistManager $datalistManager)
    {
        $this->datalistManager = $datalistManager;
    }

    public function index(Request $request)
    {
        $user = $this->getUser();

//        Setting session username
        $username = $user->getDisplayName() ? $user->getDisplayName() : $user->getUsername();
        $request->getSession()->set('userName', $username);

        // get user's datalist
        $datalists = $user->getDatalists();
        dump($datalists);

        $content = $this->renderView('home.html.twig', array(
            'datalists' => $datalists
        ));

        return new Response($content);
    }
}