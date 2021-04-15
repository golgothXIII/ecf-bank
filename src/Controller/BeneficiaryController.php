<?php

namespace App\Controller;

use App\Entity\Beneficiary;
use App\Form\BeneficiaryType;
use App\Repository\BeneficiaryRepository;
use App\Repository\UserRepository;
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
     */

    public function valideBeneficiaries( BeneficiaryRepository $beneficiaryRepository) : Response
    {

        $banker = $this->getUser()->getBanker();
        $beneficiaries = $beneficiaryRepository->findBy(['banker' => $banker, 'isValidated' => false]);

        return $this->render('beneficiary/valide_benificiary.html.twig',[
            'beneficiaries' => $beneficiaries,
            'empty_text' => 'Il n\'y aucun bénéficiaire à valider.',
            'validation' => true,
        ]);
    }

    /**
     * @Route("/valide-beneficiary/{id}", name="valide_beneficiary")
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
     */
    public function beneficiariesList(BeneficiaryRepository $beneficiaryRepository){

        $customer = $this->getUser()->getCustomer();
        $beneficiaries = $beneficiaryRepository->findBy(['customer' => $customer ]);

        return $this->render('beneficiary/beneficiaries_list.html.twig',[
            'beneficiaries' => $beneficiaries,
            'empty_text' => 'Vous n\'avez aucun bénéficiaire',
            'validation' => false,
        ]);

    }

}
