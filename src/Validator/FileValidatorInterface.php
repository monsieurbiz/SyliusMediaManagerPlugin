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

interface FileValidatorInterface
{
    /**
     * @throws InvalidTypeException
     * @throws FileNotFoundException
     * @throws InvalidMimeTypeException
     */
    public function validate(string $type, string $relativeFilePath): bool;
}
