<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransferType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = [];

        foreach ($options['data'] as $beneficiary) {
            $choices[$beneficiary->getLabel() . " - " . $beneficiary->getIBAN()] = $beneficiary->getId();
        }
        $builder
            ->add( 'beneficiary', ChoiceType::class, [
                'choices' => $choices,
                'label' => 'Bénéficiaire'
            ])
            ->add('reference', TextType::class, [
                'label' => 'Référence',
                'attr' => [ 'maxlength' => 35 ],
                ])
            ->add('label', TextareaType::class, [
                'label' => 'Libellé (140 caracteres max',
                'required' => false,
            ] )
            ->add('amount', MoneyType::class, [
                'label' => 'Montant',
                'invalid_message' => 'Valeur incorect',

                ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
