<?php

namespace App\Form;

use App\Entity\User;
use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;
use Symfony\Component\Validator\Constraints\PasswordStrength;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationFormType extends AbstractType
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
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'required' => true,
                'mapped' => false,
                'label' => 'label.password',
                'attr' => [
                    'autocomplete' => 'new-password',
                    'placeholder' => 'placeholder.password',
                    'data-icon' => 'bxs-key',
                    'data-sr-only' => false,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('message.not_blank.password', [], 'forms'),
                    ]),
                    new Length([
                        'min' => 16,
                        'minMessage' => $this->translator->trans('message.length.min.password', ['%limit%' => '{{ limit }}'], 'forms'),
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                    new PasswordStrength(),
                    new NotCompromisedPassword(),
                ],
            ])
            ->add('address', AddressType::class, [
                'required' => true,
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'required' => true,
                'mapped' => false,
                'label' => 'label.agree_terms',
                'constraints' => [
                    new IsTrue([
                        'message' => $this->translator->trans('message.is_true.agree_terms', [], 'forms'),
                    ]),
                ],
            ])
            ->add('captcha', Recaptcha3Type::class, [
                'required' => true,
                'constraints' => new Recaptcha3(),
                'action_name' => 'register',
                'locale' => 'fr',
            ])
            ->add('country', TextType::class, [
                'required' => false,
                'mapped' => false,
                'label' => 'label.honeypot',
                'attr' => [
                    'autocomplete' => 'off',
                    'tabindex' => '-1',
                    'aria-hidden' => 'true',
                    'data-icon' => 'bxs-honey',
                    'placeholder' => 'placeholder.honeypot',
                    'data-sr-only' => true,
                ],
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
