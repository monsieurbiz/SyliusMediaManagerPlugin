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
use MonsieurBiz\SyliusMediaManagerPlugin\Exception\FileNotCreatedException;
use MonsieurBiz\SyliusMediaManagerPlugin\Exception\FileNotUploadedException;
use MonsieurBiz\SyliusMediaManagerPlugin\Operator\DirectoryOperatorInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;

#[AsLiveComponent]
final class UploadManager
{
    use ComponentToolsTrait;
    use ModalUtilityTrait;

    #[LiveProp]
    public bool $loaded = false;

    #[LiveProp(updateFromParent: true)]
    public ?string $absoluteDirectoryPath = null;

    #[LiveProp(updateFromParent: true)]
    public array $mimeTypes = [];

    #[LiveProp(updateFromParent: true)]
    public string $maxFileSize = '';

    #[LiveProp(updateFromParent: true)]
    public string $fileType = '';

    #[LiveProp]
    public ?string $fileUploadError = null;

    #[LiveProp]
    public array $fileUploadErrorParameters = [];

    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly DirectoryOperatorInterface $directoryOperator,
    ) {
    }

    public function __invoke(): void
    {
        if (null === $this->absoluteDirectoryPath) {
            return;
        }

        $this->loaded = true;
        $this->fileUploadError = null;
        $this->fileUploadErrorParameters = [];
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    #[LiveAction]
    public function upload(
        Request $request
    ): void {
        if (null === $this->absoluteDirectoryPath) {
            return;
        }

        try {
            $this->fileUploadError = null;
            $this->fileUploadErrorParameters = [];

            /** @var ?UploadedFile $uploadedFile */
            $uploadedFile = $request->files->get('file');
            if (null === $uploadedFile) {
                return;
            }

            $this->validateUploadedFile($uploadedFile);
            $this->directoryOperator->addUploadedFile($this->absoluteDirectoryPath, $uploadedFile); /** @phpstan-ignore-line */
            $this->emit('reloadCurrentDirectory', componentName: 'MediaManager:SelectionModal');
        } catch (FileNotCreatedException $e) {
            $this->fileUploadError = 'monsieurbiz_sylius_media_manager.error.cannot_upload_file';
            $this->fileUploadErrorParameters = ['message' => $e->getMessage()];
        } catch (FileNotUploadedException $e) {
            $this->fileUploadError = $e->getMessage();
        }
    }

    private function validateUploadedFile(UploadedFile $uploadedFile): void
    {
        $errors = $this->validator->validate($uploadedFile, [
            new Assert\NotBlank([
                'message' => 'monsieurbiz_sylius_media_manager.error.cannot_upload_file',
            ]),
            new Assert\File([
                'maxSize' => $this->maxFileSize,
                'uploadIniSizeErrorMessage' => 'monsieurbiz_sylius_media_manager.error.max_file_size',
                'mimeTypes' => $this->mimeTypes,
                'mimeTypesMessage' => \sprintf('monsieurbiz_sylius_media_manager.error.invalid_mime_type.%s', $this->fileType),
            ]),
        ]);

        if (\count($errors) > 0) {
            throw new FileNotUploadedException((string) ($errors[0]?->getMessage() ?? ''));
        }
    }
}
