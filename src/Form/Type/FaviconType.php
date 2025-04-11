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
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class FaviconType extends AbstractType
{
    public function __construct(
        #[Autowire(param: 'monsieurbiz_sylius_media_manager.max_file_size.favicon')]
        private readonly string $maxFileSize
    ) {
    }

    public function getBlockPrefix(): string
    {
        return 'monsieurbiz_sylius_media_manager_favicon';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefault('file_type', FileInterface::TYPE_FAVICON);
        $resolver->setDefault('max_file_size', $this->maxFileSize);
        $resolver->setDefault('folder', 'gallery/icons');
        $resolver->setDefault('filter_width', 50); // The width of the preview filter
    }

    public function getParent(): string
    {
        return ImageType::class;
    }
}
