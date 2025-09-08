<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class UploadFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('upload', FileType::class, [
                'required' => true,
                'multiple' => false,
                'label' => 'label.file',
                'attr' => [
                    'data-icon' => 'bxs-file',
                    'data-sr-only' => false,
                    'enctype' => 'multipart/form-data',
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '64M',
                        'extensions' => [
                            'pdf', 'doc', 'docx', 'xls', 'xlsx', 'png', 'jpg', 'jpeg', 'gif', 'txt', 'zip'
                        ],
                        'extensionsMessage' => 'Please upload a valid file (PDF, DOC, DOCX, XLS, XLSX, PNG, JPG, GIF, TXT, ZIP).',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'forms',
        ]);
    }
}
