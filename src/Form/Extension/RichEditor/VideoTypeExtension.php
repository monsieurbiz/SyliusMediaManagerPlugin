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

namespace MonsieurBiz\SyliusMediaManagerPlugin\Form\Extension\RichEditor;

use Closure;
use MonsieurBiz\SyliusMediaManagerPlugin\Form\Type\ImageType as MediaManagerImageType;
use MonsieurBiz\SyliusMediaManagerPlugin\Form\Type\VideoType as MediaManagerVideoType;
use MonsieurBiz\SyliusRichEditorPlugin\Form\Type\UiElement\VideoType as RichEditorVideoType;
use ReflectionFunction;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;

class VideoTypeExtension extends AbstractTypeExtension
{
    /**
     * @SuppressWarnings(UnusedFormalParameter)
     * @SuppressWarnings(CyclomaticComplexity)
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('video', MediaManagerVideoType::class)
            ->add('image', MediaManagerImageType::class)
        ;

        foreach ($builder->getEventDispatcher()->getListeners(FormEvents::PRE_SUBMIT) as $listener) {
            if ($listener instanceof Closure) {
                $reflection = new ReflectionFunction($listener);
                $closureScopeClass = $reflection->getClosureScopeClass();

                if (null === $closureScopeClass) {
                    continue;
                }
                /** @phpstan-ignore-next-line  */
                if (RichEditorVideoType::class === $closureScopeClass->getName()) {
                    // Remove the event listener that is re-adding the field and emptying the options
                    $builder->getEventDispatcher()->removeListener(FormEvents::PRE_SUBMIT, $listener);
                }
            }
        }
    }

    public static function getExtendedTypes(): iterable
    {
        /** @phpstan-ignore-next-line  */
        return [RichEditorVideoType::class];
    }
}
