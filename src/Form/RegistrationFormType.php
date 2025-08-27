<?php

namespace App\Form;

use App\Entity\User;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaV3Type;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrueV3;
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

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'label' => 'label.name',
                'attr' => [
                    'data-icon' => 'bxs-user',
                    'placeholder' => 'placeholder.name',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'message.not_blank.name',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'required' => true,
                'label' => 'label.email',
                'attr' => [
                    'data-icon' => 'bxs-envelope',
                    'placeholder' => 'placeholder.email',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'message.not_blank.email',
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
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'message.not_blank.phone',
                    ]),
                    new Length([
                        'min' => 10,
                        'max' => 14,
                        'minMessage' => 'message.length.min_message.phone',
                        'maxMessage' => 'message.length.max_message.phone',
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
                ],
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 6,
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
                        'message' => 'message.is_true.agree_terms',
                    ]),
                ],
            ])
            ->add('recaptcha', EWZRecaptchaV3Type::class, [
                'action_name' => 'register',
                'constraints' => [
                    new IsTrueV3()
                ]
            ]) ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'translation_domain' => 'forms',
        ]);
    }
}
