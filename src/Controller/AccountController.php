<?php

namespace App\Controller;

use App\Repository\AccountRepository;
use App\Repository\TransferRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    /**
     * @Route("/account", name="account")
     */
    public function index(): RedirectResponse
    {
        return $this->redirectToRoute('account_page', [ 'page' => 1 ]);

        $account = $this->getUser()->getCustomer()->getAccount();
    }

    /**
     * @Route("account/{page}", name= "account_page")
     */
    public function accountPage(
        int $page,
        TransferRepository $transferRepository
    ) {

        $account = $this->getUser()->getCustomer()->getAccount();
        $paginationLimite =  $this->getParameter('paginationLimite');
        $nbRow = $transferRepository->numberOfTransfers($account);
        $lastPage = ceil( $nbRow / $paginationLimite );

        // if the page is out of bounds redirect to first hors last page
        if ( $page > $lastPage) {
            return $this->redirectToRoute('account_page', [ 'page' => $lastPage ]);
        }
        if ( $page < 1 ) {
            return $this->redirectToRoute('account_page', [ 'page' => 1 ]);
        }

        $transfers = $transferRepository->findBy(
            ['account' => $account],
            ['transfer_date' => 'desc'],
            $paginationLimite,
            $paginationLimite * ( $page - 1 )
        );

        return $this->render('account/index.html.twig', [
            'transfers' => $transfers,
            'balance' => $transferRepository->findBalanceAccount($account),
            'currentPage' => $page,
            'lastPage' => $lastPage,
            'routeName' => 'account_page',
        ]);
    }
}
