<?php

namespace TelegramBundle\Controller;

use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\Ogg;
use FFMpeg\Format\Video\WebM;
use FFMpeg\Format\Video\X264;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use TelegramBundle\Helper\FileHelper;

class PublicController extends Controller
{
    /**
     * @Route("/home", name="sky")
     */
    public function indexAction()
    {
        $points = array();
        $fluxs = $this->getActiveFlux();
        foreach ($fluxs as $flux){
            $points[] = array(
                "flux" => array("name" => $flux->getName(),"color" => $flux->getColor(),"tid"=>$flux->getTid()),
                "points" => $this->getPointsFromFlux($flux));
        }
        return $this->render('TelegramBundle:Flux:index.html.twig',array("points" => $points));
        //return $this->redirectToRoute('flux', array('tid' => '-123732933'), 301);
    }

    /**
     * @Route("/", name="index")
     */
    public function mapAction()
    {
        $points = array();
        $fluxs = $this->getActiveFlux();
        $actualFluxs = $this->getActualFlux();
        $oldFluxs = $this->getInActiveFlux();
        $events = $this->getActiveEvent();
        $oldEvents = $this->getInActiveEvent();
        usort($oldFluxs, array($this, "cmp"));
        usort($actualFluxs, array($this, "cmp"));
        foreach ($fluxs as $flux){
            $pointList = $this->getPointsFromFlux($flux);
            $points[] = array(
                "flux" => array("name" => $flux->getName(),"color" => $flux->getColor(),"tid"=>$flux->getTid()),
                "points" => $pointList);
            $lastPoint = end($pointList);
            $flux->lastDate = $lastPoint['date'];
        }
        return $this->render('TelegramBundle:Flux:map.html.twig',array("points" => $points,
            "fluxs" => $fluxs, "actualfluxs" => $actualFluxs, "oldfluxs" => $oldFluxs,
            "events" => $events, "oldevents" => $oldEvents));
        //return $this->redirectToRoute('flux', array('tid' => '-123732933'), 301);
    }
    /**
     * @Route("/post/{tid}")
     */
     public function postAction($tid){
       $em = $this->getDoctrine()->getManager();
       $m = $em->getRepository("TelegramBundle:Message")->findOneByTid($tid);
       return $this->render('TelegramBundle:Flux:post.html.twig',array("m" => $m));
     }

    /**
     * @Route("/etoile", name="etoile")
     */
     public function etoileAction(){
         $em = $this->getDoctrine()->getManager();
         $flux = $em->getRepository("TelegramBundle:Flux")->findOneByTid(-242381742);
         $pointList = $this->getPointsFromFlux($flux);
         $offset = 0; // 0, 601(video), 100(audio)
         $messages = $this->getArrayOfMessage($flux,10,$offset);
         return $this->render('TelegramBundle:Flux:etoile.html.twig',array("points" => $pointList, "messages" => $messages, "flux" => $flux));
     }
     /**
      * @Route("/etoile/points", name="etoilePoints")
      */
     public function etoilePointsAction(){
         $em = $this->getDoctrine()->getManager();
         $flux = $em->getRepository("TelegramBundle:Flux")->findOneByTid(-242381742);  // -242381742 // -245837434
         $pointList = $this->getPointsFromFlux($flux);
          return $this->json(array('points' => $pointList));
     }

    /**
     * @Route("/exhome", name="exindex")
     */
    public function skyAction()
    {
        $points = array();
        $fluxs = $this->getActiveFlux();
        foreach ($fluxs as $flux){
            $points[] = array(
                "flux" => array("name" => $flux->getName(),"color" => $flux->getColor(),"tid"=>$flux->getTid()),
                "points" => $this->getPointsFromFlux($flux));
        }
        return $this->render('TelegramBundle:Flux:index.html.twig',array("points" => $points));
    }

    /**
     * @Route("/flux/{tid}/{offset}", name="flux", defaults={"offset" = null} )
     */
    public function fluxAction($tid,$offset)
    {
        $em = $this->getDoctrine()->getManager();
        $flux = $em->getRepository("TelegramBundle:Flux")->findOneBy(array("tid" => $tid));
        if(!$flux){
            throw $this->createNotFoundException('The flux does not exist');
        }

        $dates = $this->getDatesFromFlux($flux);
        $points = $this->getPointsFromFlux($flux);
        $messages = $this->getArrayOfMessage($flux,10,$offset);
        return $this->render('TelegramBundle:Flux:flux.html.twig',array(
            "points" => $points,
            "messages" => $messages,
            "flux" => $flux,
            "dates" => $dates));
    }

    /**
     * @Route("/ajax/flux/{tid}/{offset}", defaults={"offset" = 0} )
     */
    public function fluxAjaxAction($tid,$offset)
    {
        $em = $this->getDoctrine()->getManager();
        $flux = $em->getRepository("TelegramBundle:Flux")->findOneBy(array("tid" => $tid));
        if(!$flux){
            throw $this->createNotFoundException('The flux does not exist');
        }

        $points = $this->getPointsFromFlux($flux);
        if($offset < 0){
            $limit = 10 + $offset;
            $offset = 0;
        }else{
            $limit = 10;
        }
        $messages = $this->getArrayOfMessage($flux,$limit,$offset);
        return $this->render('TelegramBundle:Flux:ajax.html.twig',array(
            "points" => $points,
            "messages" => $messages,
            "flux" => $flux,
            "offset" => $offset));
    }

    private function getArrayOfMessage($flux,$limit,$offset){
        $em = $this->getDoctrine()->getManager();
        return $em->getRepository("TelegramBundle:Message")->findBy(
            array("flux" => $flux, "active" => 1),
            array("date" => "desc"),
            $limit,
            $offset
        );
    }

    private function cmp($a, $b)
    {
            return strcmp($a->getCreationDate()->getTimestamp(), $b->getCreationDate()->getTimestamp());

    }

    /**
     * @Route("/about", name="about")
     */
    public function aboutAction(){
        return $this->render('TelegramBundle:Flux:about.html.twig');
    }
    private function getActiveFlux(){
        $em = $this->getDoctrine()->getManager();
        return $em->getRepository("TelegramBundle:Flux")->findBy(array("active" => true));
    }

    private function getInActiveFlux(){
        $em = $this->getDoctrine()->getManager();
        return $em->getRepository("TelegramBundle:Flux")->findBy(array("archive" => true),array('startDate' => 'ASC'));
    }
    private function getActualFlux(){
        $em = $this->getDoctrine()->getManager();
        return $em->getRepository("TelegramBundle:Flux")->findBy(array("archive" => false,"active" => true),array('startDate' => 'ASC'));
        //echo time();
        //return $em->createQuery("SELECT e FROM TelegramBundle:Flux e WHERE 'archive' <> 1 AND 'archive' <> TRUE AND 'archive' <> '1' ")->getResult();
    }
    private function getActiveEvent(){
        $em = $this->getDoctrine()->getManager();
        return $em->createQuery('SELECT e FROM TelegramBundle:Event e WHERE e.endDate > CURRENT_DATE()')->getResult();
    }
    private function getInActiveEvent(){
        $em = $this->getDoctrine()->getManager();
        return $em->createQuery('SELECT e FROM TelegramBundle:Event e WHERE e.endDate < CURRENT_DATE()')->getResult();
         //$em->getRepository("TelegramBundle:Event")->findBy(array("active" => true && "endDate" < time() ));
    }
    private function getPointsFromFlux($flux){
        $em = $this->getDoctrine()->getManager();
        $messages = $em->getRepository("TelegramBundle:Message")->findBy(array("flux" => $flux,"active" => true));
        $points = array();
        $length = count($messages);
        foreach ($messages as $key => $message){
            if($message->getType() == "location"){
                $u = $message->getUser();
                $points[] = array(
                    "tid" => $message->getTid(),
                    "lat" => $message->getLat(),
                    "lon" => $message->getLon(),
                    "loc" => $message->getLocality(),
                    "key" => $length - $key - 1,
                    "username" => $u->getFName() ." ". $u->getLName(),
                    "date" => $message->getDate()->format("d M y, G:i")
                );
            }
        }
        return $points;
    }
    private function getDatesFromFlux($flux){
        $em = $this->getDoctrine()->getManager();
        $messages = $em->getRepository("TelegramBundle:Message")->findBy(array("flux" => $flux));
        $last = count($messages) - 1;
        return array("first" => $messages[0]->getDate(),"last" => $messages[$last]->getDate());
    }

    /**
     * @Route("/just/updating")
     */
    public function justUpdatingAction(){
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository("TelegramBundle:User")->find(1);
        $messages = $em->getRepository("TelegramBundle:Message")->findBy(array("user" => $user));
        foreach ($messages as $m){
            $m->setActive(false);
            $em->persist($m);
        }
        $em->flush();
    }

    /**
     * @Route("/just/updating2")
     */
    public function justUpdatinggAction(){

        $files = scandir("files/videos");
        foreach ($files as $file) {
            $ext = FileHelper::getExtension($file);
            if($ext == "mp4") {

                $id = explode(".",$file)[0];
                $ffmpeg = FFMpeg::create();
                $video = $ffmpeg->open("files/videos/" . $id . ".mp4");

                $video->save(new WebM(), "files/videos/" . $id . ".webm");
                $video->save(new Ogg(), "files/videos/" . $id . ".ogg");
                echo "mp4";
            }
        }
        $response = new Response("Helo"
        );

        return $response;
    }


}
