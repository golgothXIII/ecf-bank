<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Customer;
use App\Entity\User;
use App\Form\AddCustomerType;
use App\Repository\BankerRepository;
use App\Repository\UserRepository;
use App\Services\myServices;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AddCustomerController extends AbstractController
{
    /**
     * @Route("/add-customer", name="add_customer")
     */
    public function index(
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

        return $this->render('add_customer/index.html.twig', [
            'form' => $form->createView(),
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
