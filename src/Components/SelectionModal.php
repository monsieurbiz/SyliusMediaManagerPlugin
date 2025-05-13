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

use MonsieurBiz\SyliusMediaManagerPlugin\Exception\FolderNotCreatedException;
use MonsieurBiz\SyliusMediaManagerPlugin\Exception\FolderNotRenamedException;
use MonsieurBiz\SyliusMediaManagerPlugin\Model\FileInterface;
use MonsieurBiz\SyliusMediaManagerPlugin\Operator\DirectoryOperatorInterface;
use MonsieurBiz\SyliusMediaManagerPlugin\Resolver\FilePathResolverInterface;
use Symfony\Component\Filesystem\Path;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class SelectionModal
{
    use ComponentToolsTrait;
    use DefaultActionTrait;
    use ModalUtilityTrait;

    #[LiveProp]
    public ?string $inputName = null;

    #[LiveProp]
    public ?string $absoluteDirectoryPath = null;

    #[LiveProp]
    public ?string $baseFolderPath = null;

    #[LiveProp]
    public string $folder = '';

    #[LiveProp]
    public string $fileType = FileInterface::TYPE_FILE;

    #[LiveProp]
    public array $mimeTypes = [];

    #[LiveProp]
    public string $maxFileSize = '';

    #[LiveProp]
    public ?string $successMessage = null;

    #[LiveProp]
    public ?string $errorMessage = null;

    #[LiveProp]
    public array $errorParameters = [];

    #[LiveProp(writable: true)]
    public string $folderName = '';

    public function __construct(
        private readonly FilePathResolverInterface $filePathResolver,
        private readonly DirectoryOperatorInterface $directoryOperator,
    ) {
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    #[LiveListener('displayFileSelection')]
    public function init(
        #[LiveArg]
        ?string $inputName,
        #[LiveArg]
        ?string $filePath,
        #[LiveArg]
        string $baseFolderPath,
        #[LiveArg]
        string $fileType,
        #[LiveArg]
        array $mimeTypes,
        #[LiveArg]
        string $maxFileSize,
    ): void {
        if (null === $inputName) {
            return;
        }

        $this->inputName = $inputName;
        $this->fileType = $fileType;
        $this->mimeTypes = $mimeTypes;
        $this->maxFileSize = $maxFileSize;
        $this->baseFolderPath = $baseFolderPath;

        $relativeDirectoryPath = null === $filePath || '' === $filePath ? $this->baseFolderPath : Path::getDirectory($filePath);
        $absoluteDirectoryPath = $this->filePathResolver->getAbsoluteFilePath($relativeDirectoryPath);
        if (false === $this->directoryOperator->exists($absoluteDirectoryPath)) {
            $absoluteDirectoryPath = $this->filePathResolver->getAbsoluteFilePath($baseFolderPath);
            $this->directoryOperator->createDir($absoluteDirectoryPath);
        }

        $this->absoluteDirectoryPath = $absoluteDirectoryPath;
        $this->load();
        $this->openSelectionModal();
    }

    #[LiveListener('setCurrentDirectory')]
    public function setCurrentDirectory(#[LiveArg] string $absoluteDirectoryPath): void
    {
        $this->absoluteDirectoryPath = $absoluteDirectoryPath;
        $this->load();
    }

    #[LiveListener('renameCurrentDirectory')]
    public function renameCurrentDirectory(#[LiveArg] string $newDirectoryName): void
    {
        if (null === $this->absoluteDirectoryPath) {
            return;
        }

        try {
            $this->absoluteDirectoryPath = $this->directoryOperator->renameDirectory($this->absoluteDirectoryPath, $newDirectoryName);
            $this->successMessage = 'monsieurbiz_sylius_media_manager.ui.rename_folder_success';
            $this->load();
        } catch (FolderNotRenamedException $e) {
            $this->errorMessage = 'monsieurbiz_sylius_media_manager.error.cannot_rename_folder';
        }
    }

    #[LiveListener('addSubDirectoryToCurrentDirectory')]
    public function addSubDirectoryToCurrentDirectory(#[LiveArg] string $subDirectoryName): void
    {
        if (null === $this->absoluteDirectoryPath) {
            return;
        }

        try {
            $newDirectoryPath = Path::join($this->absoluteDirectoryPath, $subDirectoryName);
            $this->directoryOperator->createDirectory($newDirectoryPath);
            $this->absoluteDirectoryPath = $newDirectoryPath;
            $this->successMessage = 'monsieurbiz_sylius_media_manager.ui.create_folder_success';
            $this->load();
        } catch (FolderNotCreatedException $e) {
            $this->errorMessage = 'monsieurbiz_sylius_media_manager.error.cannot_create_folder';
        }
    }

    #[LiveListener('reloadCurrentDirectory')]
    public function load(): void
    {
        if (null === $this->absoluteDirectoryPath) {
            return;
        }

        $this->successMessage = null;
        $this->errorMessage = null;
        $this->errorParameters = [];
    }

    #[LiveListener('deleteFile')]
    public function deleteFile(#[LiveArg] string $fileName): void
    {
        if (null === $this->absoluteDirectoryPath) {
            return;
        }

        $absoluteFilePath = Path::join($this->absoluteDirectoryPath, $fileName);
        $this->directoryOperator->deleteFile($absoluteFilePath);
        $this->successMessage = 'this file has been deleted';
        $this->load();
    }

    #[LiveListener('displayError')]
    public function displayError(
        #[LiveArg]
        string $errorMessage,
        #[LiveArg]
        array $errorParameters = [],
    ): void {
        $this->errorMessage = $errorMessage;
        $this->errorParameters = $errorParameters;
    }
}
