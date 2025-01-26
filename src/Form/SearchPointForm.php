<?php

declare(strict_types=1);

namespace App\Form;

use App\Form\Transformer\CityTransformer;
use App\Model\Address;
use App\Validator\ZipCodeRequired;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class SearchPointForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->setMethod('GET')
            ->add('street', TextType::class, [
                'label' => 'Street',
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'max' => 64
                    ]),
                ]
            ])
            ->add('city', TextType::class, [
                'label' => 'City',
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 3,
                        'max' => 64
                    ]),
                ]
            ])
            ->add('postCode', TextType::class, [
                'label' => 'Postal code',
                'constraints' => [
                    new Regex([
                        'pattern' => '/^\d{2}-\d{3}$/',
                        'message' => 'The postal code must be in the format XX-XXX.'
                    ]),
                ]
            ])
            ->get('city')->addModelTransformer(new CityTransformer())
        ;

        $builder
            ->addEventListener(
                FormEvents::PRE_SUBMIT,
                [$this, 'onPreSubmit']
            )
        ;
    }

    public function onPreSubmit(PreSubmitEvent $event): void
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (null === $data['postCode']) {
            return;
        }

        if ('01-234' !== $data['postCode']) {
            return;
        }

        $form
            ->add('name', TextType::class, [
                'label' => 'Name',
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
            'constraints' => [
                new ZipCodeRequired()
            ]
        ]);
    }
}
