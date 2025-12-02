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

namespace MonsieurBiz\SyliusMediaManagerPlugin\Model;

class File implements FileInterface
{
    private string $name = '';

    private string $type;

    private ?string $mimeType = null;

    private ?string $link = null;

    private ?string $path = null;

    private bool $deletable = false;

    private bool $selectable = false;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(?string $mimeType): void
    {
        $this->mimeType = $mimeType;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): void
    {
        $this->link = $link;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): void
    {
        $this->path = $path;
    }

    public function isDeletable(): bool
    {
        return $this->deletable;
    }

    public function setDeletable(bool $deletable): void
    {
        $this->deletable = $deletable;
    }

    public function isSelectable(): bool
    {
        return $this->selectable;
    }

    public function setSelectable(bool $selectable): void
    {
        $this->selectable = $selectable;
    }
}
