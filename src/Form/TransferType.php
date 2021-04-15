<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
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
//dd($choices);
        $builder
            ->add( 'beneficiary', ChoiceType::class, [
                'choices' => $choices,
            ])
            ->add('reference', TextType::class)
            ->add('label', TextType::class, ['label' => 'Libellé'] )
            ->add('amount', MoneyType::class, [ 'label' => 'Montant'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
