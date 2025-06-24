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

namespace MonsieurBiz\SyliusMediaManagerPlugin\Components\SelectionModal;

use MonsieurBiz\SyliusMediaManagerPlugin\Components\ModalUtilityTrait;
use MonsieurBiz\SyliusMediaManagerPlugin\Resolver\FilePathResolverInterface;
use Symfony\Component\Filesystem\Path;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;

#[AsLiveComponent(route: 'sylius_admin_live_component')]
class DirectoryManager
{
    use ComponentToolsTrait;
    use ModalUtilityTrait;

    #[LiveProp]
    public bool $loaded = false;

    #[LiveProp]
    public bool $canAddSubfolder = true;

    #[LiveProp]
    public bool $canRename = false;

    #[LiveProp(updateFromParent: true)]
    public ?string $absoluteDirectoryPath = null;

    #[LiveProp(updateFromParent: true)]
    public ?string $relativeRootDirectoryPath = null;

    #[LiveProp]
    public ?string $relativeDirectoryPath = null;

    #[LiveProp(writable: true)]
    public ?string $currentDirectoryName = null;

    #[LiveProp]
    public ?string $currentParentDirectoryPath = null;

    #[LiveProp(writable: true)]
    public ?string $newDirectoryName = null;

    #[LiveProp(writable: true)]
    public ?string $directoryNewName = null;

    #[LiveProp]
    public bool $isDirectoryAddMode = false;

    #[LiveProp]
    public bool $isDirectoryRenameMode = false;

    #[LiveProp]
    public bool $isEditionMode = false;

    public function __construct(private readonly FilePathResolverInterface $filePathResolver)
    {
    }

    public function __invoke(): void
    {
        if (null === $this->absoluteDirectoryPath) {
            return;
        }

        $this->relativeDirectoryPath = $this->filePathResolver->getRelativeFilePath($this->absoluteDirectoryPath);
        $this->currentParentDirectoryPath = Path::getDirectory($this->relativeDirectoryPath) . '/';
        $this->currentDirectoryName = ltrim(str_replace(Path::getDirectory($this->relativeDirectoryPath), '', $this->relativeDirectoryPath), '/');

        $this->canRename = $this->relativeDirectoryPath != $this->relativeRootDirectoryPath;
        $this->isDirectoryAddMode = false;
        $this->isDirectoryRenameMode = false;
        $this->loaded = true;
    }

    #[LiveAction]
    public function enabledDirectoryAddMode(): void
    {
        $this->isDirectoryAddMode = true;
        $this->isEditionMode = true;
    }

    #[LiveAction]
    public function disabledDirectoryAddMode(): void
    {
        $this->isDirectoryAddMode = false;
        $this->isEditionMode = false;
        $this->newDirectoryName = null;
    }

    #[LiveAction]
    public function enabledDirectoryRenameMode(): void
    {
        $this->isDirectoryRenameMode = true;
        $this->isEditionMode = true;
        $this->directoryNewName = $this->currentDirectoryName;
    }

    #[LiveAction]
    public function disabledDirectoryRenameMode(): void
    {
        $this->isDirectoryRenameMode = false;
        $this->isEditionMode = false;
        $this->directoryNewName = null;
    }

    #[LiveAction]
    public function renameDirectory(): void
    {
        if ($this->currentDirectoryName === $this->directoryNewName) {
            $this->disabledDirectoryRenameMode();

            return;
        }

        $this->emit('displayConfirmAction', [
            'message' => 'monsieurbiz_sylius_media_manager.ui.rename_folder_confirm',
            'messageParameters' => ['%path%' => $this->currentDirectoryName],
            'reconfirmMessage' => 'monsieurbiz_sylius_media_manager.ui.rename_folder_confirm_bis',
            'confirmLabel' => 'monsieurbiz_sylius_media_manager.ui.rename_folder',
            'confirmedEventName' => 'renameCurrentDirectory',
            'confirmedEventParams' => ['newDirectoryName' => $this->directoryNewName],
            'confirmedEventComposantName' => 'MediaManager:SelectionModal',
            'openModalAfterClose' => self::SELECTION_MODAL_NAME,
        ], 'MediaManager:ConfirmationModal');
        $this->disabledDirectoryRenameMode();
    }

    #[LiveAction]
    public function addSubDirectory(): void
    {
        $this->emit('addSubDirectoryToCurrentDirectory', [
            'subDirectoryName' => $this->newDirectoryName,
        ], 'MediaManager:SelectionModal');
        $this->disabledDirectoryAddMode();
    }
}
