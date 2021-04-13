<?php

namespace App\Controller;

use App\Entity\Beneficiary;
use App\Form\BeneficiaryType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AddBeneficiaryController extends AbstractController
{
    /**
     * @Route("/add-beneficiary", name="add_beneficiary")
     */
    public function index(
        Request $request,
        ValidatorInterface $validator
    ): Response
    {

        $form = $this->createForm(BeneficiaryType::class);

        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) {
            $data = $form->getData();
            $beneficiary = (new Beneficiary())
                ->setLabel($data['label'])
                ->setIBAN($data['IBAN'])
                ->setBIC($data['BIC'])
            ;
            $errors = $validator->validate($beneficiary);

            return $this->render('add_beneficiary/index.html.twig', [
                'form' => $form->createView(),
                'errors' => $errors,
            ]);

            return $this->render('displayInfo.html.twig', [
                'title' => 'Demande de création d\'un bénéficiaire effectuer.',
                'contentTitle' => 'Votre demande a été prise en compte',
                'content' => [ 'Votre demande a bien été prise en compte.',
                    'Après vérification le bénéficiaire sera validé par l\'un de nos collaborateurs',
                    'Merci pour votre confiance et a bientôt',
                ],
            ]);

        }


        return $this->render('add_beneficiary/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
