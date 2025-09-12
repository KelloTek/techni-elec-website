<?php

namespace App\Form;

use App\Entity\Address;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;
use Symfony\Component\Validator\Constraints\PasswordStrength;
use Symfony\Contracts\Translation\TranslatorInterface;

class SettingsType extends AbstractType
{
    public function __construct(private TranslatorInterface $translator) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'label' => 'label.name',
                'attr' => [
                    'data-icon' => 'bxs-user',
                    'placeholder' => 'placeholder.name',
                    'data-sr-only' => false,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('message.not_blank.name', [], 'forms'),
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'required' => true,
                'label' => 'label.email',
                'attr' => [
                    'data-icon' => 'bxs-envelope',
                    'placeholder' => 'placeholder.email',
                    'data-sr-only' => false,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('message.not_blank.email', [], 'forms'),
                    ]),
                ],
            ])
            ->add('phone', TelType::class, [
                'required' => true,
                'label' => 'label.phone',
                'attr' => [
                    'data-phone-input' => true,
                    'data-icon' => 'bxs-phone',
                    'placeholder' => 'placeholder.phone',
                    'data-sr-only' => false,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('message.not_blank.phone', [], 'forms'),
                    ]),
                    new Length([
                        'min' => 10,
                        'max' => 14,
                        'minMessage' => $this->translator->trans('message.length.min.phone', ['%limit%' => '{{ limit }}'], 'forms'),
                        'maxMessage' => $this->translator->trans('message.length.max.phone', ['%limit%' => '{{ limit }}'], 'forms'),
                    ]),
                ],
            ])
            ->add('address', AddressType::class, [
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'translation_domain' => 'forms',
        ]);
    }
}
