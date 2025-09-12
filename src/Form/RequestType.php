<?php

namespace App\Form;

use App\Entity\File;
use App\Entity\Request;
use App\Entity\RequestCategory;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class RequestType extends AbstractType
{
    public function __construct(private TranslatorInterface $translator) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'required' => true,
                'label' => 'label.title',
                'attr' => [
                    'data-icon' => 'bxs-tag',
                    'placeholder' => 'placeholder.title',
                    'data-sr-only' => false,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('message.not_blank.title', [], 'forms'),
                    ]),
                ],
            ])
            ->add('content', TextType::class, [
                'required' => true,
                'label' => 'label.content',
                'attr' => [
                    'data-icon' => 'bxs-message',
                    'placeholder' => 'placeholder.content',
                    'data-sr-only' => false,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('message.not_blank.content', [], 'forms'),
                    ]),
                ],
            ])
            ->add('category', EntityType::class, [
                'class' => RequestCategory::class,
                'choice_label' => 'label',
                'attr' => [
                    'data-icon' => 'bxs-message',
                    'placeholder' => 'placeholder.content',
                    'data-sr-only' => false,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Request::class,
            'translation_domain' => 'forms',
        ]);
    }
}
