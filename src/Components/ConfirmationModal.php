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

use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class ConfirmationModal
{
    use ComponentToolsTrait;
    use DefaultActionTrait;
    use ModalUtilityTrait;

    #[LiveProp]
    public bool $isLoaded = false;

    #[LiveProp]
    public string $message = '';

    #[LiveProp]
    public array $messageParameters = [];

    #[LiveProp]
    public ?string $reconfirmMessage = null;

    #[LiveProp]
    public array $reconfirmMessageParameters = [];

    #[LiveProp]
    public string $confirmLabel = '';

    #[LiveProp]
    public string $confirmedEventName = '';

    #[LiveProp]
    public array $confirmedEventParams = [];

    #[LiveProp]
    public string $confirmedEventComposantName = '';

    #[LiveProp]
    public bool $isReconfirmation = false;

    #[LiveProp]
    public ?string $openModalAfterClose = null;

    #[LiveListener('displayConfirmAction')]
    public function init(
        #[LiveArg]
        string $message,
        #[LiveArg]
        string $confirmLabel,
        #[LiveArg]
        string $confirmedEventName,
        #[LiveArg]
        string $confirmedEventComposantName,
        #[LiveArg]
        array $messageParameters = [],
        #[LiveArg]
        ?string $reconfirmMessage = null,
        #[LiveArg]
        array $reconfirmMessageParameters = [],
        #[LiveArg]
        array $confirmedEventParams = [],
        #[LiveArg]
        ?string $openModalAfterClose = null,
    ): void {
        $this->message = $message;
        $this->messageParameters = $messageParameters;
        $this->reconfirmMessage = $reconfirmMessage;
        $this->reconfirmMessageParameters = $reconfirmMessageParameters;

        $this->confirmLabel = $confirmLabel;
        $this->confirmedEventName = $confirmedEventName;
        $this->confirmedEventParams = $confirmedEventParams;
        $this->confirmedEventComposantName = $confirmedEventComposantName;
        $this->openModalAfterClose = $openModalAfterClose;

        $this->isLoaded = true;
        $this->closeSelectionModal();
        $this->openConfirmationModal();
    }

    #[LiveAction]
    public function confirm(): void
    {
        $this->emit(
            $this->confirmedEventName,
            $this->confirmedEventParams,
            $this->confirmedEventComposantName
        );
        $this->close();
    }

    #[LiveAction]
    public function reconfirm(): void
    {
        $this->isReconfirmation = true;
    }

    #[LiveAction]
    public function close(): void
    {
        $this->closeConfirmationModal();
        if (null !== $this->openModalAfterClose) {
            $this->dispatchBrowserEvent('media_manager:modal:open', ['modalName' => $this->openModalAfterClose]);
        }
        $this->reset();
    }

    protected function reset(): void
    {
        $this->messageParameters = [];
        $this->reconfirmMessage = null;
        $this->reconfirmMessageParameters = [];
        $this->confirmedEventParams = [];
        $this->openModalAfterClose = null;
        $this->isReconfirmation = false;
    }
}
