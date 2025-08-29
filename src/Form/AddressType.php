<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class AddressType extends AbstractType
{
    public function __construct(private TranslatorInterface $translator) {}

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
                    'data-sr-only' => false,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('message.not_blank.address.line', [], 'forms'),
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
                    'data-sr-only' => false,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('message.not_blank.address.zip_code', [], 'forms'),
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
                    'data-sr-only' => false,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('message.not_blank.address.city', [], 'forms'),
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
