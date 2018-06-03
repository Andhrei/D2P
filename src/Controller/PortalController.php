<?php
/**
 * Created by PhpStorm.
 * User: Administrator
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
        $content = $this->renderView('base.html.twig');

        return new Response($content);
    }
}