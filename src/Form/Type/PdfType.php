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

use MonsieurBiz\SyliusMediaManagerPlugin\Helper\FileHelperInterface;
use MonsieurBiz\SyliusMediaManagerPlugin\Provider\MimeTypesProviderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Webmozart\Assert\Assert;

final class PdfType extends TextType
{
    public function __construct(
        private MimeTypesProviderInterface $mimeTypesProvider,
    ) {
    }

    public function getBlockPrefix(): string
    {
        return 'monsieurbiz_sylius_media_manager_pdf';
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        parent::buildView($view, $form, $options);
        $fileType = $options['file-type'];
        Assert::string($fileType);
        $view->vars['folder'] = $options['folder'];
        $view->vars['fileType'] = $fileType;
        $view->vars['mimeTypes'] = implode(',', $this->mimeTypesProvider->getMimeTypesByType($fileType));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'folder' => 'gallery/pdfs', // Keep empty the use `/public/media` as root.
            'file-type' => FileHelperInterface::TYPE_PDF, // The wanted file type managed by FileHelper,
        ]);
    }
}
