<?php

namespace App\Controller;

use App\Form\BeneficiaryType;
use App\Form\TransferType;
use App\Repository\BeneficiaryRepository;
use App\Repository\TransferRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TransferController extends AbstractController
{
    /**
     * @Route("/transfer", name="transfer")
     */
    public function index(
        BeneficiaryRepository $beneficiaryRepository
    ): Response
    {

        $user = $this->getUser();
        $beneficiaries = $beneficiaryRepository->findBy([ 'customer' => $user->getCustomer() ]);
        $form = $this->createForm(TransferType::class, $beneficiaries);

        return $this->render('transfer/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
