<?php
/**
 * Created by PhpStorm.
 * LdapUser: Administrator
 * Date: 5/31/2018
 * Time: 3:26 PM
 */

namespace App\Controller;

use App\Entity\Datalist;
use App\Entity\LdapUser;
use App\Manager\DatalistManager;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\Process\Process;


class PortalController extends BaseController
{
    private $datalistManager;

    public function __construct(DatalistManager $datalistManager)
    {
        $this->datalistManager = $datalistManager;
    }

    public function restore($sessionId){
        $sessionId = substr_replace($sessionId,'/',4,0);
        $sessionId = substr_replace($sessionId,'/',7,0);
        $sessionId =  substr_replace($sessionId,'-',10,0);


        $cmd = 'omnidb -session '.$sessionId;
        $proc = new Process($cmd);
        $proc->run();
        $output = $proc->getOutput();

        preg_match_all("/.*?(?=:)/",$output, $host);
        $cmd = 'omnir -host '.$host[0][0].' -session '.$sessionId;
        dump($cmd);
        $proc2 = new Process($cmd);
        $proc2->start();

        return $this->redirectToRoute('portal_home');
    }

    public function index(Request $request)
    {
        $user = $this->getUser();

//        Setting session username
        $username = $user->getDisplayName() ? $user->getDisplayName() : $user->getUsername();
        $request->getSession()->set('userName', $username);

        // get user's datalist
        $datalists = $user->getDatalists();

        $sessions = new ArrayCollection();
        foreach ($datalists as $datalist) {
            $datalist->setSessions($this->getSessions($datalist));
        }

        $content = $this->renderView('home.html.twig', array(
            'datalists' => $datalists,
        ));

        return new Response($content);
    }

    public function getSessions(Datalist $datalist) {
        $cmd = 'omnidb -session -datalist '.$datalist->getName().' -detail';
        $proc = new Process($cmd);
        $proc->run();
        $output = $proc->getOutput();

        preg_match_all("/(?<=SessionID : ).*(?!\\r)/",$output, $sessionIds);
        preg_match_all("/(?<=Session type        : ).*(?!\r)/",$output, $sessionTypes);
        preg_match_all("/(?<=Started             : ).*(?!\r)/",$output, $sessionDates);
        preg_match_all("/(?<=Status              : ).*(?!\r)/",$output, $sessionStatus);

        $sessions = array();
        for ($i = 0 ; $i <= count($sessionIds[0])-1 ; $i = $i +1 ) {
            $session = array(
                'id' => str_replace('/','',str_replace('-','',trim($sessionIds[0][$i]))),
                'type' => trim($sessionTypes[0][$i]),
                'date' => trim($sessionDates[0][$i]),
                'status' => trim($sessionStatus[0][$i]),
            );
            $sessions[$i]=$session;
        }

        return array_reverse($sessions);

    }

}