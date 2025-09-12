<?php

namespace App\Form;

use App\Entity\RequestCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class RequestCategoryType extends AbstractType
{
    public function __construct(private TranslatorInterface $translator) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('label', TextType::class, [
                'required' => true,
                'label' => 'label.label',
                'attr' => [
                    'data-icon' => 'bxs-tag',
                    'placeholder' => 'placeholder.label',
                    'data-sr-only' => false,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('message.not_blank.label', [], 'forms'),
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RequestCategory::class,
            'translation_domain' => 'forms',
        ]);
    }
}
