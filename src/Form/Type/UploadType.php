<?php

/*
 * This file is part of Monsieur Biz' Media Manager plugin for Sylius.
 *
 * (c) Monsieur Biz <sylius@monsieurbiz.com>
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MonsieurBiz\SyliusMediaManagerPlugin\Form\Type;

use MonsieurBiz\SyliusMediaManagerPlugin\Provider\MimeTypesProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

final class UploadType extends AbstractType
{
    public function __construct(
        private string $defaultMaxFileSize,
        private array $maxFileSizesByType,
        private MimeTypesProviderInterface $mimeTypesProvider,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var ?string $type */
        $type = $options['type'];
        $maxFileSize = $this->maxFileSizesByType[$type] ?? $this->defaultMaxFileSize;
        $builder
            ->add('file', FileType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'monsieurbiz_sylius_media_manager.error.cannot_upload_file',
                    ]),
                    new File([
                        'maxSize' => $maxFileSize,
                        'uploadIniSizeErrorMessage' => 'monsieurbiz_sylius_media_manager.error.max_file_size',
                        'mimeTypes' => $this->mimeTypesProvider->getMimeTypesByType($type),
                        'mimeTypesMessage' => 'monsieurbiz_sylius_media_manager.error.invalid_mime_type.' . $options['type'],
                    ]),
                ],
            ])
            ->add('folder', TextType::class)
            ->add('path', TextType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'type' => null,
        ]);
        $resolver->setAllowedTypes('type', ['null', 'string']);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
