<?php

namespace App\Form;

use App\Entity\Discussion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class DiscussionType extends AbstractType
{
    public function __construct(private TranslatorInterface $translator) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextType::class, [
                'required' => true,
                'label' => 'label.message',
                'attr' => [
                    'data-icon' => 'bxs-message',
                    'placeholder' => 'placeholder.message',
                    'data-sr-only' => false,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('message.not_blank.message', [], 'forms'),
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Discussion::class,
            'translation_domain' => 'forms',
        ]);
    }
}
