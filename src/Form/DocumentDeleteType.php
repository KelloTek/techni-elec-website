<?php

namespace App\Form;

use App\Entity\Document;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class DocumentDeleteType extends AbstractType
{
    public function __construct(private TranslatorInterface $translator) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'mapped' => false,
                'label' => 'label.name',
                'attr' => [
                    'data-icon' => 'bxs-tag',
                    'placeholder' => 'placeholder.confirm_delete.name',
                    'data-sr-only' => false,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('message.not_blank.confirm_delete', [], 'forms'),
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Document::class,
            'translation_domain' => 'forms',
        ]);
    }
}
