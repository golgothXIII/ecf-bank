<?php

namespace App\Controller;

use App\Entity\Transfer;
use App\Form\TransferType;
use App\Repository\BeneficiaryRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TransferController extends AbstractController
{
    /**
     * @Route("/transfer", name="transfer")
     * @IsGranted("ROLE_VALIDATED_CUSTOMER")
     */
    public function index(
        BeneficiaryRepository $beneficiaryRepository,
        ValidatorInterface $validator,
        Request $request
    ): Response
    {

        $customer = $this->getUser()->getCustomer();
        $beneficiaries = $beneficiaryRepository->findBy([
            'customer' => $customer,
            'isValidated' => true,
        ]);

        if(empty($beneficiaries)) {
            return $this->render('displayInfo.html.twig', [
                'title' => 'Aucun bénéficiaire',
                'contentTitle' => 'Vous ne disposez d\'aucun bénéficiaire',
                'content' => [ 'Vous ne pouvez faire de virement tant que vous n\'avez pas créé un bénéficiaire et que celui-ci soit validé par un banquier',
                    'Veuillez en créer au moins un.',
                ],
            ]);

        }
        $form = $this->createForm(TransferType::class, $beneficiaries);
        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) {
            $data = $form->getData();
            $beneficiary = $beneficiaryRepository->findOneBy([ 'id' => $data['beneficiary'] ] );
            $transfer = (new Transfer())
                ->setLabel($data['label'])
                ->setReference($data['reference'])
                ->setAmount($data['amount'])
                ->setAccount($customer->getAccount())
                ->setBeneficiary($beneficiary)
            ;

            $errors = $validator->validate($transfer);


            if ( count($errors) > 0) {
                foreach ($errors as $error) {
                    $form[$error->getPropertyPath()]->addError(new FormError($error->getMessage()));
                }
                return $this->render('transfer/index.html.twig', [
                    'form' => $form->createView(),
                ]);
            }


            $date = new \DateTime(date("Y-m-d h:i:s", time()));
            $transfer->setTransferDate($date);
            //in the account history a transfer is negative
            $transfer->setAmount(-$transfer->getAmount());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager-> persist($transfer);
            $entityManager->flush();

            return $this->render('displayInfo.html.twig', [
                'title' => 'Virement effectué.',
                'contentTitle' => 'Votre virement a bien été effectué',
                'content' => [ 'Votre virement a bien été effectué.',
                    'Merci pour votre confiance et a bientôt',
                ],
            ]);

        }
        return $this->render('transfer/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
