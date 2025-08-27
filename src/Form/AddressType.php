<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('line', TextType::class, [
                'required' => true,
                'label' => 'label.address.line',
                'attr' => [
                    'data-address-line-input' => true,
                    'data-icon' => 'bxs-directions',
                    'placeholder' => 'placeholder.address.line',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'message.not_blank.address.line',
                    ]),
                ],
            ])
            ->add('zipCode', NumberType::class, [
                'required' => true,
                'label' => 'label.address.zip_code',
                'attr' => [
                    'data-address-zip-code-input' => true,
                    'data-icon' => 'bx-hashtag',
                    'placeholder' => 'placeholder.address.zip_code',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'message.not_blank.address.zip_code',
                    ]),
                ],
            ])
            ->add('city', TextType::class, [
                'required' => true,
                'label' => 'label.address.city',
                'attr' => [
                    'data-address-city-input' => true,
                    'data-icon' => 'bxs-city',
                    'placeholder' => 'placeholder.address.city',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'message.not_blank.address.city',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
            'translation_domain' => 'forms',
        ]);
    }
}
