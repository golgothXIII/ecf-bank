<?php

namespace App\Controller;

use App\Repository\AccountRepository;
use App\Repository\TransferRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    /**
     * @Route("/account", name="account")
     */
    public function index(
        TransferRepository $transferRepository
    ): Response
    {
        $account = $this->getUser()->getCustomer()->getAccount();
        $transfers = $transferRepository->findBy( ['account' => $account]);
//        dd($transfers);







        return $this->render('account/index.html.twig', [
            'transfers' => $transfers,
        ]);
    }
}
