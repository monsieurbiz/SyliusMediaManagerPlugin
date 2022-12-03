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

final class InvalidTypeException extends Exception
{
    private string $type;

    private array $allowedTypes;

    public function __construct(string $type, array $allowedTypes)
    {
        $this->type = $type;
        $this->allowedTypes = $allowedTypes;

        parent::__construct(sprintf('Invalid type `%s`, allowed types : %s', $type, implode(', ', $allowedTypes)));
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getAllowedTypes(): array
    {
        return $this->allowedTypes;
    }
}
