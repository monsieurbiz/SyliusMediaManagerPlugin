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

namespace MonsieurBiz\SyliusMediaManagerPlugin\Validator;

use MonsieurBiz\SyliusMediaManagerPlugin\Exception\FileNotFoundException;
use MonsieurBiz\SyliusMediaManagerPlugin\Exception\InvalidMimeTypeException;
use MonsieurBiz\SyliusMediaManagerPlugin\Exception\InvalidTypeException;
use MonsieurBiz\SyliusMediaManagerPlugin\Model\FileInterface;
use MonsieurBiz\SyliusMediaManagerPlugin\Provider\MimeTypesProviderInterface;
use MonsieurBiz\SyliusMediaManagerPlugin\Repository\FileRepositoryInterface;
use MonsieurBiz\SyliusMediaManagerPlugin\Resolver\FilePathResolverInterface;
use Symfony\Component\Filesystem\Filesystem;

readonly class FileValidator implements FileValidatorInterface
{
    public function __construct(
        private MimeTypesProviderInterface $mimeTypesProvider,
        private Filesystem $filesystem,
        private FilePathResolverInterface $filePathResolver,
        private FileRepositoryInterface $fileRepository,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function validate(string $type, string $relativeFilePath): bool
    {
        if (!\in_array($type, FileInterface::FILE_TYPES, true)) {
            throw new InvalidTypeException($type, FileInterface::FILE_TYPES);
        }

        $absoluteFilePath = $this->filePathResolver->getAbsoluteFilePath($relativeFilePath);
        if (false === $this->filesystem->exists($absoluteFilePath)) {
            throw new FileNotFoundException($relativeFilePath);
        }

        $file = $this->fileRepository->findOneFromPath($absoluteFilePath);
        $mimeType = $file->getMimeType();
        $allowedMimeTypes = $this->mimeTypesProvider->getMimeTypesByType($type);
        if (!\in_array($mimeType, $allowedMimeTypes, true)) {
            throw new InvalidMimeTypeException($allowedMimeTypes, $mimeType ?? 'unknown');
        }

        return true;
    }
}
