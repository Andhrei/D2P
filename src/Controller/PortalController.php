<?php
/**
 * Created by PhpStorm.
 * LdapUser: Administrator
 * Date: 5/31/2018
 * Time: 3:26 PM
 */

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;


class PortalController extends BaseController
{
    public function index(Request $request)
    {
        $username = $this->getUser()->getDisplayName() ? $this->getUser()->getDisplayName() : $this->getUser()->getUsername();
        $request->getSession()->set('userName', $username);
        $content = $this->renderView('home.html.twig');

        return new Response($content);
    }
}