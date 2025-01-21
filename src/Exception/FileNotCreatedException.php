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

namespace MonsieurBiz\SyliusMediaManagerPlugin\Exception;

use Exception;

final class FileNotCreatedException extends Exception
{
    private string $filename;

    private string $errorMessage;

    public function __construct(string $filename, string $errorMessage)
    {
        $this->filename = $filename;
        $this->errorMessage = $errorMessage;
        parent::__construct(\sprintf('File `%s` couldn\'t be created. Error : %s', $filename, $errorMessage));
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
}
