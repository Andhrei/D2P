<?php

namespace App\Controller;

use App\Entity\Datalist;
use App\Entity\File;
use App\Entity\Folder;
use App\Form\FileRestoreType;
use App\Manager\DatalistManager;
use App\Manager\FolderManager;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RestoreController extends Controller
{
    private $datalistManager;
    private $folderManager;

    public function __construct(DatalistManager $datalistManager, FolderManager $folderManager)
    {
        $this->datalistManager = $datalistManager;
        $this->folderManager = $folderManager;
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

        $content = $this->renderView('restore/index.html.twig', array(
            'datalists' => $datalists,
        ));

        return new Response($content);
    }

    public function list($sessionId, Request $request) {
        $sessionId = substr_replace($sessionId,'/',4,0);
        $sessionId = substr_replace($sessionId,'/',7,0);
        $sessionId =  substr_replace($sessionId,'-',10,0);

        $cmd = 'omnidb -session '.$sessionId.' -detail';

        $proc = new Process($cmd);
        $proc->run();
        $output = $proc->getOutput();


        preg_match("/(?<=Object name : ).*(?!\\r)/",$output, $objectName);

        $objectName = substr_replace($objectName[0],'',-1,2);
        $objectName = str_replace("'","\"",$objectName);
        $cmd = 'omnidb -session '.$sessionId.' -winfs '.$objectName.' -catalog';

        $proc = new Process($cmd);
        $proc->run();
        $output = $proc->getOutput();

        $results = array();
        preg_match_all("/(?<=M    ).*/",$output, $elements);
        foreach  ($elements[0] as $element)
        {
            $files =  array_reverse(explode("/",$element));
            for ($i = 0; $i < count($files) ; $i++) {
                    if(preg_match("/\.\w*/",$files[$i])) {
                        /* it's a file */
                        $results[$element] = $element;
                    }
                }
        }


        $file = array();
        $form = $this->createFormBuilder($file)
            ->add('file', ChoiceType::class,array(
                'choices' => $results)
            )
            ->add('save', SubmitType::class, array('label' => 'Restore file'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() ) {
            $cmd = 'omnir -winfs '.$objectName.'  -session '.$sessionId.' -tree '.substr_replace($results[array_keys($results)[0]],'',-1,2);
            dump($cmd);

            $proc = new Process($cmd);
            $proc->start();

            $this->redirect('portal_home');
        }

        return $this->render('restore/fileRestore.html.twig', array(
            'form' => $form->createView(),
            'formMethod' => 'POST',
            'formUrl' => 'api_post_schedule',
            'class' => 'Schedule',
        ));

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
