<?php

namespace App\Controller;

use App\Entity\Account;
use App\Repository\BankerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    /**
     * @Route("/test", name="test")
     */
    public function index( BankerRepository $bankerRepository): Response
    {

        dd($bankerRepository->findBankerWithLeastAccount());
        return $this->render('displayInfo.html.twig', [
            'title' => 'Demande de création de compte effectuer.',
            'contentTitle' => 'Votre demande a été prise en compte',
            'content' => [ date("ymd", time()),
                substr( date("ymd", time() ) , -5 ) . "00001",
                'Merci pour votre confiance et a bientôt',
            ],
        ]);
    }
}
