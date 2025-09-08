<?php

namespace App\Form;

use App\Entity\Document;
use App\Entity\DocumentCategory;
use App\Entity\File;
use App\Entity\Request;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class DocumentType extends AbstractType
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
                    'placeholder' => 'placeholder.name_document',
                    'data-sr-only' => false,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('message.not_blank.name', [], 'forms'),
                    ]),
                ],
            ])
            ->add('file', UploadFileType::class, [
                'required' => true,
                'mapped' => false,
            ])
            ->add('category', EntityType::class, [
                'class' => DocumentCategory::class,
                'label' => 'label.category',
                'choice_label' => 'label',
                'attr' => [
                    'data-icon' => 'bxs-user',
                    'data-sr-only' => false,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('message.not_blank.name', [], 'forms'),
                    ]),
                ],
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'label' => 'label.user',
                'choice_label' => 'name',
                'attr' => [
                    'data-icon' => 'bxs-user',
                    'data-sr-only' => false,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('message.not_blank.name', [], 'forms'),
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
