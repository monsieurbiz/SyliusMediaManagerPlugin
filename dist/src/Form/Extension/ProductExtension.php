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

namespace App\Form\Extension;

use MonsieurBiz\SyliusMediaManagerPlugin\Form\Type\AudioType;
use MonsieurBiz\SyliusMediaManagerPlugin\Form\Type\FaviconType;
use MonsieurBiz\SyliusMediaManagerPlugin\Form\Type\FileType;
use MonsieurBiz\SyliusMediaManagerPlugin\Form\Type\ImageType;
use MonsieurBiz\SyliusMediaManagerPlugin\Form\Type\PdfType;
use MonsieurBiz\SyliusMediaManagerPlugin\Form\Type\VideoType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

class ProductExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('imagePath', ImageType::class, [
                'label' => 'Image',
                'required' => false,
            ])
            ->add('videoPath', VideoType::class, [
                'required' => false,
            ])
            ->add('pdfPath', PdfType::class, [
                'required' => false,
            ])
            ->add('faviconPath', FaviconType::class, [
                'required' => false,
            ])
            ->add('audioPath', AudioType::class, [
                'required' => false,
            ])
            ->add('filePath', FileType::class, [
                'required' => false,
            ])
        ;
    }

    public static function getExtendedTypes(): iterable
    {
        return [ProductType::class];
    }
}
