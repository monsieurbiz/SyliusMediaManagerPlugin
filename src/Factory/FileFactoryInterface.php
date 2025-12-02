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

namespace MonsieurBiz\SyliusMediaManagerPlugin\Factory;

use MonsieurBiz\SyliusMediaManagerPlugin\Model\FileInterface;
use SplFileInfo;

interface FileFactoryInterface
{
    public function createFromSplFileInfo(SplFileInfo $splFileInfo): FileInterface;

    public function createParentLinkFile(string $absoluteDirectoryPath): FileInterface;
}
