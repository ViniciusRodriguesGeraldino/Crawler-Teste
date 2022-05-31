<?php

namespace App\Controller;
use App\Entity\Empresa;
use App\Entity\Players;
use App\Entity\PlayersOnline;
use App\Entity\PlayersOnlineHoras;
use App\Repository\CfopNaoMovimentaRepository;
use App\Repository\PlayersOnlineHorasRepository;
use App\Repository\PlayersOnlineRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PlayersRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer; 
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\DomCrawler\Crawler;
use App\Service\CrawlerService;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Notifier\Bridge\Discord\DiscordOptions;
use Symfony\Component\Notifier\Bridge\Discord\Embeds\DiscordEmbed;
use Symfony\Component\Notifier\Bridge\Discord\Embeds\DiscordFieldEmbedObject;
use Symfony\Component\Notifier\Bridge\Discord\Embeds\DiscordFooterEmbedObject;
use Symfony\Component\Notifier\Bridge\Discord\Embeds\DiscordMediaEmbedObject;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Notifier\ChatterInterface;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;


/**
* @Route("/account",name="account")
*/
class AccountController extends AbstractController
{

    /**
     * @Route("/", name="account_index")
     */
    public function account(): Response
    {
        $error = "";
        return $this->render('account/index.html.twig', [
            'error'         => $error,
        ]);
    }    


    /**
     * @Route("/welcome", name="welcome")
     */
    public function welcome(): Response
    {
        $error = "";
        return $this->render('account/index.html.twig', [
            'error'         => $error,
        ]);
    }        
    
 

}
