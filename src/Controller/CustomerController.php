<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Customer;
use App\Entity\User;
use App\Form\AddCustomerType;
use App\Repository\AccountRepository;
use App\Repository\BankerRepository;
use App\Repository\CustomerRepository;
use App\Repository\UserRepository;
use App\Services\myServices;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CustomerController extends AbstractController
{
    /**
     * @Route("/add-customer", name="customer")
     */
    public function addCustomer(
        Request $request,
        UserRepository $userRepository,
        BankerRepository $bankerRepository,
        UserPasswordEncoderInterface $passwordEncoder,
        myServices $services
    ): Response
    {

        $form = $this->createForm(AddCustomerType::class);

        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) {
            $data = $form->getData();
            $thereAreErrors = false;
            // we check if the email is already in database
            if ( $userRepository->findOneBy(['email' => $data['email'], ]) ) {
                $form['email']->addError( new FormError('Cette adresse mail est dèjà utilisée'));
                $thereAreErrors = true;
            }
            // check password complexity
            if (! $services->passwordIsComplex($data['password'])) {
                $form['password']->addError( new FormError('Le mot de passe répondre au exigences de complexité'));
                $thereAreErrors = true;
            }
            // check file type ( image or pdf )
            if ( ! $this->fileVerification($data['file'])) {
                $form['file']->addError( new FormError('Ce type de fichier n\'est pas autorisé'));
                $thereAreErrors = true;
            }
            // no errors detected continue process
            if ( ! $thereAreErrors ) {
                // create the new user (user, Customer & Account class )
                $user = new User();
                $customer = new Customer();
                $account = new Account();

                $user->setEmail($data['email']);
                $user->setPassword($passwordEncoder->encodePassword($user, $data['password'] ));
                $user->setRoles(['ROLE_CUSTOMER']);
                $user->setLastname($data['lastname']);
                $user->setFirstname($data['firstname']);
                $user->setCustomer($customer);

                $customer->setBirthday($data['birthday']);
                $customer->setAdress($data['adress']);
                $customer->setZipCode($data['zipCode']);
                $customer->setCity($data['city']);
                // give a name to the file and move it in the directory define inparameter
                $fichier= md5(uniqid()) . time() . '.' . $data['file']->guessExtension();
                $customer->setIdPath($fichier);
                $data['file']->move(
                    $this->getParameter('id_images_directory'),
                    $fichier
                );
                $customer->setUser($user);
                $customer->setAccount($account);

                $account->setCustomer($customer);
                $account->setBanker($bankerRepository->findBankerWithLeastAccount());

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
                // all is good and inform user
                return $this->render('displayInfo.html.twig', [
                    'title' => 'Demande de création de compte effectuer.',
                    'contentTitle' => 'Votre demande a été prise en compte',
                    'content' => [ 'Votre demande a bien été prise en compte.',
                        'Après vérification votre compte sera validé par l\'un de nos collaborateurs',
                        'Merci pour votre confiance et a bientôt',
                    ],
                ]);
            }
        }

        return $this->render('customer/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/customer-delete", name="customer_delete")
     * @IsGranted("ROLE_VALIDATED_CUSTOMER")
     */
    public function delCustomer(
        Request $request,
        CustomerRepository $customerRepository
    ): Response
    {
        if(empty($this->getUser())) {
            return $this->render('displayInfo.html.twig', [
                'title' => 'Demande de suppresion de compte effectuer.',
                'contentTitle' => 'Votre demande a été prise en compte',
                'content' => [ 'Votre demande a bien été prise en compte.',
                    'Après vérification votre compte sera supprimé par l\'un de nos collaborateurs',
                    'Merci pour votre confiance et a bientôt',
                ],
            ]);

        }

        // create form
        $form = $this->createFormBuilder()
            ->add(
                'checkConfirmation',
                CheckboxType::class,
                [
                    'label' => 'Veuillez confirmer votre demande de suppression de compte en cochant cette case',
                        'attr' => [
                        // Add JS for enabled en disable confirm button
                        'onchange' => 'document.getElementById(\'confirmButton\').disabled = !this.checked;'
                    ]
                ])
            ->getForm();

        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) {
            $data = $form->getData();
            if($data['checkConfirmation']) {

                $user = $this->getUser();
                $user->setRoles(['ROLE_TO_DELETED_CUSTOMER']);

                $user->getCustomer()->getAccount()->setToDeleted(true);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

                return $this->redirectToRoute('customer_delete');
            }
        }

        return $this->render('customer/customer_delete.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/customer-delete-validation", name="customer_delete_validation")
     * @IsGranted("ROLE_BANKER")
     */
    public function customerDeleteValidation() : Response
    {
        return $this->redirectToRoute('customer_delete_validation_page', [ 'page' => 1 ]);
    }

    /**
     * @Route("/customer-delete-validation/{page}", name="customer_delete_validation_page")
     * @IsGranted("ROLE_BANKER")
     */
    public function customerDeleteValidationPage(
        int $page,
        AccountRepository $accountRepository,
        Request $request
    ) : Response
    {
        $banker = $this->getUser()->getBanker();

        $paginationLimite =  $this->getParameter('paginationLimite');
        $nbRow = $accountRepository->numberOfAccountToDelete($banker);

        // case where there is still no transfer made
        $lastPage = $nbRow == 0 ? 1 : ceil( $nbRow / $paginationLimite);
        // if the page is out of bounds redirect to first hors last page
        if ( $page > $lastPage) {
            return $this->redirectToRoute($request->attributes->get('_route'), [ 'page' => $lastPage ]);
        }
        if ( $page < 1 ) {
            return $this->redirectToRoute($request->attributes->get('_route'), [ 'page' => 1 ]);
        }

        $accounts = $accountRepository->findBy([
            'banker' => $banker,
            'toDeleted' => true ],
            [],
            $paginationLimite,
            $paginationLimite * ( $page - 1 )
        );
        return $this->render('customer/customer_delete_validation.html.twig', [
            'accounts' => $accounts,
            'routeName' => $request->attributes->get('_route'),
            'currentPage' => $page,
            'lastPage' => $lastPage,
        ]);
    }



    /**
     * @Route("/customer-delete/{id}")
     * @IsGranted("ROLE_BANKER")
     */
    public function customerDelete(
        int $id,
        AccountRepository $accountRepository,
        EntityManagerInterface $entityManager
    ) : Response
    {

        $account = $accountRepository->findOneBy([
           'bank_account_id' => $id,
            'toDeleted' => true,
        ]);
        if ( empty($account) ) {
            return $this->render('displayInfo.html.twig', [
                'title' => 'Erreur de suppression.',
                'contentTitle' => 'Erreur lors de la suppression du compte',
                'content' => [ 'Erreur lors de la suppression du compte.',
                    'Veuillez vérifier la demande de suppresion et réessayé la confirmation de suppresion',
                ],
            ]);

        }

        $entityManager->remove($account->getCustomer()->getUser());
        $entityManager->flush();

        return $this->render('displayInfo.html.twig', [
            'title' => 'Suppresion de compte effectuer.',
            'contentTitle' => 'Le compte a été supprimé',
            'content' => [ 'Le compte N° ' . $account->getBankAccountId() . ' a bien supprimé.',
            ],
        ]);
    }

    /**
     * function checks if the file type is allowed
     *
     * @param UploadedFile $file file to verify
     *
     * @return boolean
     */
    private function fileVerification(UploadedFile $file) : bool {
        $mineType = mime_content_type($file->getRealPath());
        return preg_match($this->getParameter('id_images_allowed'), $mineType );
    }

}
