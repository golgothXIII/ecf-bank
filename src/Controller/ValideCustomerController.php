<?php

namespace App\Controller;

use App\Repository\AccountRepository;
use App\Repository\CustomerRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ValideCustomerController extends AbstractController
{
    /**
     * @Route("/valide-customers", name="valide_customers")
     * @IsGranted("ROLE_BANKER")
     */
    public function index(): Response
    {
        return $this->redirectToRoute('valide_customers_page', [ 'page' => 1 ]);
    }

    /**
     * @Route("/valide-customers/{page}", name="valide_customers_page")
     * @IsGranted("ROLE_BANKER")
     */
    public function valideCustomerPage(
        int $page,
        AccountRepository $accountRepository,
        Request $request
    ): Response
    {

        $banker = $this->getUser()->getBanker();
        $paginationLimite =  $this->getParameter('paginationLimite');
        $nbRow = $accountRepository->numberOfAccountTovalidate($banker);

        // case where there is still no transfer made
        $lastPage =  $nbRow == 0 ? 1 : ceil( $nbRow / $paginationLimite );

        // if the page is out of bounds redirect to first hors last page
        if ( $page > $lastPage) {
            return $this->redirectToRoute($request->attributes->get('_route'), [ 'page' => $lastPage ]);
        }
        if ( $page < 1 ) {
            return $this->redirectToRoute($request->attributes->get('_route'), [ 'page' => 1 ]);
        }

        $accounts = $accountRepository->findBy([
            'bank_account_id' => null,
            'banker' => $banker->getId() ],
            [],
            $paginationLimite,
            $paginationLimite * ( $page - 1 )
        );

        return $this->render('valide_customer/index.html.twig', [
            'accounts' => $accounts,
            'routeName' => $request->attributes->get('_route'),
            'currentPage' => $page,
            'lastPage' => $lastPage,
        ]);
    }


    /**
     * @Route("/valide-customer-id/{id}", name="download_id" )
     * @IsGranted("ROLE_BANKER")
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
     * @IsGranted("ROLE_BANKER")
     */
    public function valideAccountCustomer(
        string $id,
        CustomerRepository $customerRepository
    ) : Response
    {
        // Set the bank account id
        $account = $customerRepository->findByIdPath($id)->getAccount();
        $account->setBankAccountId();
        $account->getCustomer()->getUser()->setRoles(["ROLE_VALIDATED_CUSTOMER"]);
        // Add update in database.
        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($account);
        $entityManager->flush();
        // return to valide customer module
        return $this->redirectToRoute('valide_customers');
    }
}