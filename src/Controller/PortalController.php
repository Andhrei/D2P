<?php
/**
 * Created by PhpStorm.
 * LdapUser: Administrator
 * Date: 5/31/2018
 * Time: 3:26 PM
 */

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;


class PortalController extends BaseController
{
    public function index()
    {
        $user = $this->getUser();
        $content = $this->renderView('home.html.twig', [
            'user' => $user
        ]);

        return new Response($content);
    }
}