<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\User;
use App\Form\AddCustomerType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AddCustomerController extends AbstractController
{
    /**
     * @Route("/add-customer", name="add_customer")
     */
    public function index(Request $request, UserRepository $userRepository): Response
    {

        $form = $this->createForm(AddCustomerType::class);

        $form->handleRequest($request);


        if ( $form->isSubmitted() && $form->isValid() ) {
            $data = $form->getData();
            // we check if the email is already in database
            if ( $userRepository->findOneBy(['email' => $data['email'], ]) ) {
                $form->addError( new FormError('Cette adresse mail est dèja utilisée'));
            } else {
                //il faut vérifier la complexité du mot de passe.


                // create the new user (Customer & User class )
                $user = new User();
                $customer = new Customer();




            $entityManager = $this->getDoctrine()->getManager();
            //$entityManager->persist($user);
            //$entityManager->flush();





            $fichier= md5(uniqid()).'.'.$data['file']->guessExtension();
/*            $data['file']->move(
                $this->getParameter('id_images_directory'),
                $fichier,
            );
*/
            dd($data);

            }
        }


        return $this->render('add_customer/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
