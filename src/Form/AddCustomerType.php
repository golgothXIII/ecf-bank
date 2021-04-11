<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddCustomerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [ 'label' => 'Adresse mail' ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent etre identiques.',
                'required' => true,
                'help' => 'Doit comporter au minimum 8 caracteres (Majuscule, minuscule, chiffre et caracteres spéciaux)',
                'first_options'  => [ 'label' => 'Mot de passe' ],
                'second_options' => [ 'label' => 'Confirmer le mot de passe' ],
            ])
            ->add('lastname', TextType::class, [ 'label' => 'Nom' ] )
            ->add('firstname', TextType::class, [ 'label' => 'Prénom' ] )
            ->add('birthday', BirthdayType::class, [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'label' => 'Date de naissance',
            ])
            ->add('adress', TextType::class, [ 'label' => 'Adresse' ] )
            ->add('zipCode', IntegerType::class, [ 'label' => 'Code postal' ] )
            ->add('city', TextType::class, [ 'label' => 'Ville' ] )
            ->add('file', FileType::class, [
                'label' => 'Pièces d\'identité',
                'attr' => [ 'accept' => '.jpg, .gif, .png, .tif, .pdf' ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
