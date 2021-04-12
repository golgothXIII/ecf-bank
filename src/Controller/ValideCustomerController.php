<?php

namespace App\Controller;

use App\Repository\AccountRepository;
use App\Repository\CustomerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ValideCustomerController extends AbstractController
{
    /**
     * @Route("/valide-customer", name="valide_customer")
     */
    public function index(AccountRepository $accountRepository): Response
    {
        $user = $this->getUser();
        $accounts = $accountRepository->findBy([
            'bank_account_id' => null,
            'banker' => $user->getBanker()->getId()
        ]);
//        dd($this->getParameter('id_images_directory'));

        return $this->render('valide_customer/index.html.twig', [
            'accounts' => $accounts,
        ]);
    }

    /**
     * @Route("/valide-customer-id/{id}", name="download_id" )
     */
    public function downloadIdImage(
        string $id,
        CustomerRepository $customerRepository
    ) : Response
    {

        $path = $this->getParameter('id_images_directory');
        $filename = $customerRepository->findByIdPath($id)->getIdPath();

        $content = file_get_contents($path.'/'.$filename);

        $response = new Response();

        //set headers
        $response->headers->set('Content-Type', 'mime/type');
        $response->headers->set('Content-Disposition', 'attachment;filename="'.$filename);

        $response->setContent($content);

        return $response;
    }

    /**
     * @Route("/valide-customer/{id}")
     */
    public function valideAccountCustomer(
        string $id,
        CustomerRepository $customerRepository,
        AccountRepository  $accountRepository
    ) : Response
    {
        // Set the bank account id
        $account = $customerRepository->findByIdPath($id)->getAccount();
        $account->setBankAccountId();
        // Add update in database.
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($account);
        $entityManager->flush();
        // return to valide customer module
        return $this->redirectToRoute('valide_customer');
    }

}
