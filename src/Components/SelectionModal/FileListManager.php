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
use MonsieurBiz\SyliusMediaManagerPlugin\Exception\CannotReadFolderException;
use MonsieurBiz\SyliusMediaManagerPlugin\Exception\FileNotFoundException;
use MonsieurBiz\SyliusMediaManagerPlugin\Exception\InvalidMimeTypeException;
use MonsieurBiz\SyliusMediaManagerPlugin\Exception\InvalidTypeException;
use MonsieurBiz\SyliusMediaManagerPlugin\Model\File;
use MonsieurBiz\SyliusMediaManagerPlugin\Repository\FileRepositoryInterface;
use MonsieurBiz\SyliusMediaManagerPlugin\Resolver\FilePathResolverInterface;
use MonsieurBiz\SyliusMediaManagerPlugin\Validator\FileValidatorInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;

#[AsLiveComponent(route: 'sylius_admin_live_component')]
final class FileListManager
{
    use ComponentToolsTrait;
    use ModalUtilityTrait;

    #[LiveProp]
    public bool $loaded = false;

    #[LiveProp(updateFromParent: true)]
    public ?string $inputName = null;

    #[LiveProp(updateFromParent: true)]
    public ?string $fileType = null;

    #[LiveProp(updateFromParent: true)]
    public ?string $absoluteDirectoryPath = null;

    #[LiveProp(updateFromParent: true)]
    public ?string $relativeRootDirectoryPath = null;

    #[LiveProp]
    public int $currentPage = 1;

    #[LiveProp]
    public int $itemsPerPage = 20;

    #[LiveProp]
    public int $totalItems = 0;

    #[LiveProp]
    /**
     * @var File[]
     */
    public array $fileList = [];

    public function __construct(
        private readonly FilePathResolverInterface $filePathResolver,
        private readonly FileRepositoryInterface $fileRepository,
        private readonly FileValidatorInterface $fileValidator,
        #[Autowire(param: 'monsieurbiz_sylius_media_manager.pagination.items_per_page')]
        int $defaultItemsPerPage = 20,
    ) {
        $this->itemsPerPage = $defaultItemsPerPage;
    }

    public function __invoke(): void
    {
        if (null === $this->absoluteDirectoryPath) {
            return;
        }

        try {
            $relativeDirectoryPath = $this->filePathResolver->getRelativeFilePath($this->absoluteDirectoryPath);
            $this->totalItems = $this->fileRepository->countFromPath($this->absoluteDirectoryPath);
            $this->fileList = $this->fileRepository->findAllFromPath(
                $this->absoluteDirectoryPath,
                $relativeDirectoryPath !== $this->relativeRootDirectoryPath,
                $this->currentPage,
                $this->itemsPerPage,
            );
        } catch (CannotReadFolderException) {
            $this->emit('displayError', [
                'errorMessage' => 'monsieurbiz_sylius_media_manager.error.folder_not_readable',
                'errorParameters' => [
                    '%path%' => $this->absoluteDirectoryPath,
                ],
            ], 'MediaManager:SelectionModal');

            return;
        }

        $this->loaded = true;
    }

    #[LiveAction]
    public function setCurrentDirectory(#[LiveArg] string $absoluteDirectoryPath): void
    {
        $this->emit('setCurrentDirectory', [
            'absoluteDirectoryPath' => $absoluteDirectoryPath,
        ], 'MediaManager:SelectionModal');
        $this->loaded = false;
        $this->currentPage = 1; // Reset to first page when changing directory
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    #[LiveAction]
    public function selectFile(#[LiveArg] string $filePath): void
    {
        if (null === $this->fileType) {
            return;
        }

        try {
            $this->fileValidator->validate($this->fileType, $filePath);
        } catch (InvalidTypeException) {
            $this->emit('displayError', [
                'errorMessage' => 'monsieurbiz_sylius_media_manager.error.invalid_type_input',
            ], 'MediaManager:SelectionModal');

            return;
        } catch (FileNotFoundException) {
            $this->emit('displayError', [
                'errorMessage' => 'monsieurbiz_sylius_media_manager.error.file_not_found',
            ], 'MediaManager:SelectionModal');

            return;
        } catch (InvalidMimeTypeException) {
            $this->emit('displayError', [
                'errorMessage' => 'monsieurbiz_sylius_media_manager.error.invalid_mime_type.' . $this->fileType,
            ], 'MediaManager:SelectionModal');

            return;
        }

        $this->emit('fileSelected', [
            'filePath' => $filePath,
            'inputName' => $this->inputName,
        ], 'MediaManager:FormField');
        $this->closeAllModals();
    }

    #[LiveAction]
    public function deleteFile(#[LiveArg] string $fileName): void
    {
        $this->emit('displayConfirmAction', [
            'message' => 'monsieurbiz_sylius_media_manager.ui.delete_file_confirm',
            'messageParameters' => ['%path%' => $fileName],
            'reconfirmMessage' => 'monsieurbiz_sylius_media_manager.ui.delete_file_confirm_bis',
            'confirmLabel' => 'monsieurbiz_sylius_media_manager.ui.delete_file',
            'confirmedEventName' => 'deleteFile',
            'confirmedEventParams' => ['fileName' => $fileName],
            'confirmedEventComposantName' => 'MediaManager:SelectionModal',
            'openModalAfterClose' => self::SELECTION_MODAL_NAME,
        ], 'MediaManager:ConfirmationModal');
    }

    #[LiveAction]
    public function onFileDeleted(): void
    {
        // Refresh the file list and check if we need to go to previous page
        $this->loaded = false;

        // If current page becomes empty (except for first page), go to previous page
        $totalPages = $this->getTotalPages();
        if ($this->currentPage > 1 && $this->currentPage > $totalPages) {
            $this->currentPage = max(1, $totalPages);
        }
    }

    #[LiveAction]
    public function changeItemsPerPage(#[LiveArg] int $itemsPerPage): void
    {
        $this->itemsPerPage = $itemsPerPage;
        $this->currentPage = 1;
        $this->loaded = false;
        $this->__invoke();
    }

    #[LiveAction]
    public function goToPage(#[LiveArg] int $page): void
    {
        $totalPages = $this->getTotalPages();
        if ($page < 1) {
            $page = 1;
        } elseif ($page > $totalPages) {
            $page = $totalPages;
        }

        $this->currentPage = $page;
        $this->loaded = false;
    }

    #[LiveAction]
    public function previousPage(): void
    {
        if ($this->currentPage > 1) {
            --$this->currentPage;
            $this->loaded = false;
            $this->__invoke();
        }
    }

    #[LiveAction]
    public function nextPage(): void
    {
        if ($this->currentPage < $this->getTotalPages()) {
            ++$this->currentPage;
            $this->loaded = false;
            $this->__invoke();
        }
    }

    public function getTotalPages(): int
    {
        return (int) ceil($this->totalItems / $this->itemsPerPage);
    }

    public function hasMultiplePages(): bool
    {
        return $this->getTotalPages() > 1;
    }

    public function hasPreviousPage(): bool
    {
        return $this->currentPage > 1;
    }

    public function hasNextPage(): bool
    {
        return $this->currentPage < $this->getTotalPages();
    }
}
