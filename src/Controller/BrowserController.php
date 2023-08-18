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

namespace MonsieurBiz\SyliusMediaManagerPlugin\Controller;

use MonsieurBiz\SyliusMediaManagerPlugin\Exception\CannotReadCurrentFolderException;
use MonsieurBiz\SyliusMediaManagerPlugin\Exception\CannotReadFolderException;
use MonsieurBiz\SyliusMediaManagerPlugin\Exception\FileNotCreatedException;
use MonsieurBiz\SyliusMediaManagerPlugin\Exception\FileNotDeletedException;
use MonsieurBiz\SyliusMediaManagerPlugin\Exception\FileNotFoundException;
use MonsieurBiz\SyliusMediaManagerPlugin\Exception\FileTooBigException;
use MonsieurBiz\SyliusMediaManagerPlugin\Exception\FolderNotCreatedException;
use MonsieurBiz\SyliusMediaManagerPlugin\Exception\FolderNotDeletedException;
use MonsieurBiz\SyliusMediaManagerPlugin\Exception\InvalidMimeTypeException;
use MonsieurBiz\SyliusMediaManagerPlugin\Exception\InvalidTypeException;
use MonsieurBiz\SyliusMediaManagerPlugin\Helper\FileHelperInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

final class BrowserController extends AbstractController
{
    public function listAction(
        FileHelperInterface $fileHelper,
        Request $request,
        TranslatorInterface $translator
    ): ?Response {
        $path = (string) $request->query->get('path', '');
        $folder = (string) $request->query->get('folder', '');
        $inputName = (string) $request->query->get('inputName', '');

        try {
            $files = $fileHelper->list($path, $folder);
        } catch (CannotReadCurrentFolderException $e) {
            $path = '';
            $files = $fileHelper->list($path, $folder);
        } catch (CannotReadFolderException $e) {
            return new Response($translator->trans('monsieurbiz_sylius_media_manager.error.folder_not_readable', ['%folder%' => $e->getPath()]), Response::HTTP_BAD_REQUEST);
        }

        return $this->render('@MonsieurBizSyliusMediaManagerPlugin/Admin/MediaManager/_modal.html.twig', [
            'inputName' => $inputName,
            'folder' => $fileHelper->cleanPath($folder),
            'path' => $fileHelper->cleanPath($path),
            'files' => $files,
        ]);
    }

    public function chooseAction(
        FileHelperInterface $fileHelper,
        Request $request,
        TranslatorInterface $translator
    ): ?Response {
        $path = (string) $request->query->get('path', '');
        $folder = (string) $request->query->get('folder', '');
        $type = (string) $request->query->get('type', '');

        try {
            $fileHelper->isValid($type, $path, $folder);
        } catch (InvalidTypeException $e) {
            // The type is given by the form in `file-type` option
            return new JsonResponse([
                'error' => $translator->trans('monsieurbiz_sylius_media_manager.error.invalid_type_input'),
            ], Response::HTTP_BAD_REQUEST);
        } catch (FileNotFoundException $e) {
            // The given file does not exists
            return new JsonResponse([
                'error' => $translator->trans('monsieurbiz_sylius_media_manager.error.file_not_found'),
            ], Response::HTTP_BAD_REQUEST);
        } catch (InvalidMimeTypeException $e) {
            // The allowed mime types depending on the type above
            return new JsonResponse([
                'error' => $translator->trans('monsieurbiz_sylius_media_manager.error.invalid_mime_type.' . $type),
            ], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(['path' => $fileHelper->cleanPath($path)]);
    }

    public function uploadAction(
        FileHelperInterface $fileHelper,
        Request $request,
        TranslatorInterface $translator
    ): ?Response {
        $path = (string) $request->request->get('path', '');
        $folder = (string) $request->request->get('folder', '');
        $file = $request->files->get('file');

        if (null === $file) {
            return new JsonResponse([
                'error' => $translator->trans('monsieurbiz_sylius_media_manager.error.cannot_upload_file'),
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $fileName = $fileHelper->upload($file, $path, $folder);
        } catch (FileTooBigException $e) {
            return new JsonResponse([
                'error' => $translator->trans('monsieurbiz_sylius_media_manager.error.max_file_size', ['%maxSize%' => $e->getMaxAllowedSize()]),
            ], Response::HTTP_BAD_REQUEST);
        } catch (FileNotCreatedException $e) {
            return new JsonResponse([
                'error' => $translator->trans('monsieurbiz_sylius_media_manager.error.cannot_upload_file') . ' ' . $e->getErrorMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(['file' => $fileName]);
    }

    public function createFolderAction(
        FileHelperInterface $fileHelper,
        Request $request,
        TranslatorInterface $translator
    ): ?Response {
        $path = (string) $request->request->get('path', '');
        $folder = (string) $request->request->get('folder', '');
        $newFolder = (string) $request->request->get('newFolder', '');

        try {
            $fileName = $fileHelper->createFolder($newFolder, $path, $folder);
        } catch (FolderNotCreatedException $e) {
            return new JsonResponse([
                'error' => $translator->trans('monsieurbiz_sylius_media_manager.error.cannot_create_folder'),
            ], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(['file' => $fileName]);
    }

    public function deleteFolderAction(
        FileHelperInterface $fileHelper,
        Request $request,
        TranslatorInterface $translator
    ): ?Response {
        $path = (string) $request->request->get('path', '');
        $folder = (string) $request->request->get('folder', '');
        $parentPath = \dirname($path);

        try {
            $fileHelper->deleteFolder($path, $folder);
        } catch (FolderNotDeletedException $e) {
            return new JsonResponse([
                'error' => $translator->trans('monsieurbiz_sylius_media_manager.error.cannot_delete_folder'),
            ], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(['parentFolder' => '.' === $parentPath ? '' : $parentPath]);
    }

    public function deleteFileAction(
        FileHelperInterface $fileHelper,
        Request $request,
        TranslatorInterface $translator
    ): ?Response {
        $path = (string) $request->request->get('path', '');
        $folder = (string) $request->request->get('folder', '');
        $fileFolder = \dirname($path);

        try {
            $fileHelper->deleteFile($path, $folder);
        } catch (FileNotDeletedException $e) {
            return new JsonResponse([
                'error' => $translator->trans('monsieurbiz_sylius_media_manager.error.cannot_delete_file'),
            ], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(['folder' => '.' === $fileFolder ? '' : $fileFolder]);
    }
}
