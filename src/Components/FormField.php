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

#[AsLiveComponent(route: 'sylius_admin_live_component')]
class FormField
{
    use ComponentToolsTrait;
    use DefaultActionTrait;

    #[LiveProp(updateFromParent: true)]
    public string $inputName;

    #[LiveProp(updateFromParent: true)]
    public string $fileType;

    #[LiveProp(updateFromParent: true)]
    public string $filePath;

    #[LiveProp(updateFromParent: true)]
    public string $baseFolderPath;

    #[LiveProp(updateFromParent: true)]
    public array $mimeTypes;

    #[LiveProp(updateFromParent: true)]
    public string $maxFileSize;

    #[LiveProp(updateFromParent: true)]
    public array $extraParams;

    #[LiveAction]
    public function fileSelection(): void
    {
        $this->emit('displayFileSelection', [
            'inputName' => $this->inputName,
            'filePath' => $this->filePath,
            'baseFolderPath' => $this->baseFolderPath,
            'fileType' => $this->fileType,
            'mimeTypes' => $this->mimeTypes,
            'maxFileSize' => $this->maxFileSize,
        ], 'MediaManager:SelectionModal');
    }

    #[LiveAction]
    public function confirmRemoveFile(): void
    {
        $this->emit('displayConfirmAction', [
            'message' => 'monsieurbiz_sylius_media_manager.ui.remove_confirmation',
            'confirmLabel' => 'monsieurbiz_sylius_media_manager.ui.remove_file',
            'confirmedEventName' => 'removeFile',
            'confirmedEventParams' => [
                'inputName' => $this->inputName,
            ],
            'confirmedEventComposantName' => 'MediaManager:FormField',
        ], 'MediaManager:ConfirmationModal');
    }

    #[LiveListener('removeFile')]
    public function removeFile(
        #[LiveArg]
        string $inputName,
    ): void {
        if ($this->inputName !== $inputName) {
            return;
        }
        $this->filePath = '';
    }

    #[LiveListener('fileSelected')]
    public function setFilePath(
        #[LiveArg]
        string $inputName,
        #[LiveArg]
        string $filePath,
    ): void {
        if ($this->inputName !== $inputName) {
            return;
        }
        $this->filePath = $filePath;
        $this->emit('media-manager:file-selected', [
            'inputName' => $this->inputName,
            'filePath' => $this->filePath,
        ]);
    }
}
