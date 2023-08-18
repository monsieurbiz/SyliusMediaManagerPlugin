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
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AudioType extends TextType
{
    public function getBlockPrefix(): string
    {
        return 'monsieurbiz_sylius_media_manager_audio';
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        parent::buildView($view, $form, $options);
        $view->vars['folder'] = $options['folder'];
        $view->vars['fileType'] = $options['file-type'];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'filter-width' => 50, // The width of the preview filter
            'folder' => 'gallery/audios', // Keep empty the use `/public/media` as root.
            'file-type' => FileHelperInterface::TYPE_AUDIO, // The wanted file type managed by FileHelper
        ]);
    }
}
