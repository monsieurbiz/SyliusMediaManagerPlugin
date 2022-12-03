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

use MonsieurBiz\SyliusMediaManagerPlugin\Form\Type\ImageType as MediaManagerImageType;
use Sylius\Bundle\CoreBundle\Form\Type\Product\ProductImageType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

class ProductImageTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->remove('file');
        $builder->add('path', MediaManagerImageType::class, [
            'label' => 'sylius.form.image.file',
        ]);
    }

    public static function getExtendedTypes(): iterable
    {
        return [ProductImageType::class];
    }
}
