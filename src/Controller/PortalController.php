<?php
/**
 * Created by PhpStorm.
 * LdapUser: Administrator
 * Date: 5/31/2018
 * Time: 3:26 PM
 */

namespace App\Controller;

use App\Entity\LdapUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;


class PortalController extends BaseController
{
    public function index(Request $request)
    {
        $user = $this->getUser();

//        Setting session username
        $username = $user->getDisplayName() ? $user->getDisplayName() : $user->getUsername();
        $request->getSession()->set('userName', $username);



        $content = $this->renderView('home.html.twig', array(
        ));

        return new Response($content);
    }
}