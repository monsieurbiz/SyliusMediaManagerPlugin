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

use MonsieurBiz\SyliusMediaManagerPlugin\Model\FileInterface;
use MonsieurBiz\SyliusMediaManagerPlugin\Provider\MimeTypesProviderInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FileType extends AbstractType
{
    public function __construct(
        #[Autowire(param: 'monsieurbiz_sylius_media_manager.max_file_size.default')]
        private readonly string $maxFileSize,
        private readonly MimeTypesProviderInterface $mimeTypesProvider,
    ) {
    }

    public function getBlockPrefix(): string
    {
        return 'monsieurbiz_sylius_media_manager_file';
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        parent::buildView($view, $form, $options);

        $view->vars['baseFolderPath'] = $options['folder'];
        $view->vars['fileType'] = $options['file_type'];
        $view->vars['mimeTypes'] = $options['mime_types'];
        $view->vars['maxFileSize'] = $options['max_file_size'];
        $view->vars['extraParams'] = $options['extra_parameters'];
        $view->vars['currentMimeType'] = null;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('folder', 'gallery/files'); // rename as folder
        $resolver->setDefault('extra_parameters', []);
        $resolver->setDefault('file_type', FileInterface::TYPE_FILE);
        $resolver->setDefault('max_file_size', $this->maxFileSize);
        $resolver->setDefault('mime_types', function (Options $options): array {
            /** @var string $type */
            $type = $options['file_type'];

            return $this->mimeTypesProvider->getMimeTypesByType($type);
        });

        $resolver->setAllowedTypes('folder', 'string');
        $resolver->setAllowedTypes('extra_parameters', 'array');
        $resolver->setAllowedTypes('file_type', 'string');
        $resolver->setAllowedTypes('max_file_size', 'string');
        $resolver->setAllowedTypes('mime_types', 'array');
    }

    public function getParent(): string
    {
        return TextType::class;
    }
}
