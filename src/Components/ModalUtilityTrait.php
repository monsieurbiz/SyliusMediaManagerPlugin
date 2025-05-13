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

namespace MonsieurBiz\SyliusMediaManagerPlugin\Components;

use Symfony\UX\LiveComponent\ComponentToolsTrait;

trait ModalUtilityTrait
{
    use ComponentToolsTrait;

    public const SELECTION_MODAL_NAME = 'mediaManagerSelectionModalTarget';

    public const CONFIRMATION_MODAL_NAME = 'mediaManagerConfirmationModalTarget';

    public function openSelectionModal(): void
    {
        $this->dispatchBrowserEvent('media_manager:modal:open', ['modalName' => self::SELECTION_MODAL_NAME]);
    }

    public function closeSelectionModal(): void
    {
        $this->dispatchBrowserEvent('media_manager:modal:close', ['modalName' => self::SELECTION_MODAL_NAME]);
    }

    public function openConfirmationModal(): void
    {
        $this->dispatchBrowserEvent('media_manager:modal:open', ['modalName' => self::CONFIRMATION_MODAL_NAME]);
    }

    public function closeConfirmationModal(): void
    {
        $this->dispatchBrowserEvent('media_manager:modal:close', ['modalName' => self::CONFIRMATION_MODAL_NAME]);
    }

    public function closeAllModals(): void
    {
        $this->closeConfirmationModal();
        $this->closeSelectionModal();
    }
}
