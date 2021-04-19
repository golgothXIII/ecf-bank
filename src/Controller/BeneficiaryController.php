<?php

namespace App\Controller;

use App\Entity\Beneficiary;
use App\Form\BeneficiaryType;
use App\Repository\BeneficiaryRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BeneficiaryController extends AbstractController
{
    /**
     * @Route("/add-beneficiary", name="add_beneficiary")
     * @IsGranted("ROLE_VALIDATED_CUSTOMER")
     */
    public function index(
        Request $request,
        ValidatorInterface $validator,
        UserRepository $userRepository,
        BeneficiaryRepository $beneficiaryRepository
    ): Response
    {

        $form = $this->createForm(BeneficiaryType::class);

        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) {
            $data = $form->getData();

            $customer = $userRepository->findOneBy(['id' => $this->getUser()->getId()])->getCustomer();
            $banker = $customer->getAccount()->getBanker();

            $beneficiary = (new Beneficiary())
                ->setLabel($data['label'])
                ->setIBAN(str_replace(' ','',$data['IBAN']))
                ->setBIC($data['BIC'])
                ->setBanker($banker)
                ->setCustomer($customer)
                ->setIsValidated(false)
            ;

            $errors = $validator->validate($beneficiary);

            if ( count($errors) > 0) {
                foreach ($errors as $error) {
                    $form[$error->getPropertyPath()]->addError(new FormError($error->getMessage()));
                }

                return $this->render('beneficiary/index.html.twig', [
                    'form' => $form->createView(),
                ]);
            }


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager-> persist($beneficiary);
            $entityManager->flush();

            return $this->render('displayInfo.html.twig', [
                'title' => 'Demande de création d\'un bénéficiaire effectuer.',
                'contentTitle' => 'Votre demande a été prise en compte',
                'content' => [ 'Votre demande a bien été prise en compte.',
                    'Après vérification le bénéficiaire sera validé par l\'un de nos collaborateurs',
                    'Merci pour votre confiance et a bientôt',
                ],
            ]);

        }


        return $this->render('beneficiary/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/valide-beneficiaries", name="valide_beneficiaries")
     * @IsGranted("ROLE_BANKER")
     */

    public function valideBeneficiaries() : Response
    {
        return $this->redirectToRoute( 'valide_beneficiaries_page', [ 'page' => 1 ]);
    }

    /**
     * @Route("/valide-beneficiaries/{page}", name="valide_beneficiaries_page")
     * @IsGranted("ROLE_BANKER")
     */

    public function valideBeneficiariesPage(
        int $page,
        BeneficiaryRepository $beneficiaryRepository,
        Request $request
    ) : Response
    {

        $banker = $this->getUser()->getBanker();
        $paginationLimite =  $this->getParameter('paginationLimite');
        $nbRow = $beneficiaryRepository->numberOfBeneficiariesToValidate($banker);

        // case where there is still no transfer made
        $lastPage =  $nbRow == 0 ? 1 : ceil( $nbRow / $paginationLimite );

        // if the page is out of bounds redirect to first hors last page
        if ( $page > $lastPage) {
            return $this->redirectToRoute($request->attributes->get('_route'), [ 'page' => $lastPage ]);
        }
        if ( $page < 1 ) {
            return $this->redirectToRoute($request->attributes->get('_route'), [ 'page' => 1 ]);
        }

        $beneficiaries = $beneficiaryRepository->findBy(
            ['banker' => $banker, 'isValidated' => false],
            ['label' => 'asc'],
            $paginationLimite,
            $paginationLimite * ( $page - 1 )
        );

        return $this->render('beneficiary/valide_benificiary.html.twig',[
            'beneficiaries' => $beneficiaries,
            'empty_text' => 'Il n\'y aucun bénéficiaire à valider.',
            'validation' => true,
            'routeName' => $request->attributes->get('_route'),
            'currentPage' => $page,
            'lastPage' => $lastPage,

        ]);
    }

    /**
     * @Route("/valide-beneficiary/{id}", name="valide_beneficiary")
     * @IsGranted("ROLE_BANKER")
     */
    public function valideBeneficiary(string $id, BeneficiaryRepository $beneficiaryRepository): Response
    {
        $beneficiary = $beneficiaryRepository->findOneBy( [ 'id' => $id ] );
        $beneficiary->setIsValidated(true);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($beneficiary);
        $entityManager->flush();

        return $this->redirectToRoute('valide_beneficiaries');
    }

    /**
     * @Route("/beneficiaries", name="beneficiaries")
     * @IsGranted("ROLE_VALIDATED_CUSTOMER")
     */
    public function beneficiariesList()
    {
        return $this->redirectToRoute('beneficiaries_page', [ 'page' => 1 ]);
    }

    /**
     * @Route("/beneficiaries/{page}", name="beneficiaries_page")
     * @IsGranted("ROLE_VALIDATED_CUSTOMER")
     */
    public function beneficiariesListPage(
        int $page,
        BeneficiaryRepository $beneficiaryRepository,
        Request $request
    )
    {

        $customer = $this->getUser()->getCustomer();
        $paginationLimite =  $this->getParameter('paginationLimite');
        $nbRow = $beneficiaryRepository->numberOfBeneficiaries($customer);

        // case where there is still no transfer made
        $lastPage =  $nbRow == 0 ? 1 : ceil( $nbRow / $paginationLimite );

        // if the page is out of bounds redirect to first hors last page
        if ( $page > $lastPage) {
            return $this->redirectToRoute($request->attributes->get('_route'), [ 'page' => $lastPage ]);
        }
        if ( $page < 1 ) {
            return $this->redirectToRoute($request->attributes->get('_route'), [ 'page' => 1 ]);
        }

        $beneficiaries = $beneficiaryRepository->findBy([
            'customer' => $customer ],
            ['label' => 'asc'],
            $paginationLimite,
            $paginationLimite * ( $page - 1 )
        );

        return $this->render('beneficiary/beneficiaries_list.html.twig',[
            'beneficiaries' => $beneficiaries,
            'empty_text' => 'Vous n\'avez aucun bénéficiaire',
            'validation' => false,
            'routeName' => $request->attributes->get('_route'),
            'currentPage' => $page,
            'lastPage' =>$lastPage,
        ]);
    }
}
