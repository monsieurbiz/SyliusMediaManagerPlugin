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

namespace App\Entity\Product;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\Product as BaseProduct;
use Sylius\Component\Product\Model\ProductTranslationInterface;

#[ORM\Entity]
#[ORM\Table(name: 'sylius_product')]
class Product extends BaseProduct
{
    #[ORM\Column(name: 'image_path', type: 'string', nullable: true)]
    private ?string $imagePath;

    #[ORM\Column(name: 'video_path', type: 'string', nullable: true)]
    private ?string $videoPath;

    #[ORM\Column(name: 'pdf_path', type: 'string', nullable: true)]
    private ?string $pdfPath;

    #[ORM\Column(name: 'favicon_path', type: 'string', nullable: true)]
    private ?string $faviconPath;

    #[ORM\Column(name: 'audio_path', type: 'string', nullable: true)]
    private ?string $audioPath;

    #[ORM\Column(name: 'file_path', type: 'string', nullable: true)]
    private ?string $filePath;

    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    public function setImagePath(?string $imagePath): void
    {
        $this->imagePath = $imagePath;
    }

    public function getVideoPath(): ?string
    {
        return $this->videoPath;
    }

    public function setVideoPath(?string $videoPath): void
    {
        $this->videoPath = $videoPath;
    }

    public function getPdfPath(): ?string
    {
        return $this->pdfPath;
    }

    public function setPdfPath(?string $pdfPath): void
    {
        $this->pdfPath = $pdfPath;
    }

    public function getFaviconPath(): ?string
    {
        return $this->faviconPath;
    }

    public function setFaviconPath(?string $faviconPath): void
    {
        $this->faviconPath = $faviconPath;
    }

    public function getAudioPath(): ?string
    {
        return $this->audioPath;
    }

    public function setAudioPath(?string $audioPath): void
    {
        $this->audioPath = $audioPath;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function setFilePath(?string $filePath): void
    {
        $this->filePath = $filePath;
    }

    protected function createTranslation(): ProductTranslationInterface
    {
        return new ProductTranslation();
    }
}
